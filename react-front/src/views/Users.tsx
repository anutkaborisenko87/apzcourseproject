import {useEffect, useState} from "react";
import axiosClient from "../axios-client.ts";
import Pagination from "../components/Pagination.tsx";
import Modal from "../components/Modal.tsx";
import {useStateContext} from "../../contexts/ContextProvider.tsx";

const Users = () => {
    const {setNotification} = useStateContext();
    const [users, setUsers] = useState([]);
    const [isLoading, setIsLoading] = useState(false);
    const [paginationData, setPaginationData] = useState({
        to: 0,
        from: 0,
        total: 0,
        links: [],
        last_page: 0,
        current_page: 1,

    });
    useEffect(() => {
        getUsers();
    }, []);
    const [isModalOpen, setIsModalOpen] = useState(false);

    const handleOpenModal = () => {
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
    };
    const deactivateUser = (userId:number) => {
        if (confirm("Ви впевнені, що хочете деактивувати цього користувача?")) {
            setIsLoading(true);
            axiosClient.get(`/users/${userId}/deactivate`)
                .then(() => {
                    setIsLoading(false);
                    getUsers(paginationData.current_page);
                    setNotification({type: "success", message:"Користувача деактивовано!"});
                })
                .catch(() => {
                    setIsLoading(false);
                    setNotification({type: "error", message:"Щось пішло не так!"});
                });
        }

    }
    const deleteUser = (userId:number) => {
        if (confirm("Ви впевнені, що хочете видалити цього користувача?")) {
            setIsLoading(true);
            axiosClient.delete(`/users/${userId}/delete`)
                .then(() => {
                    setIsLoading(false);
                    getUsers(paginationData.current_page);
                    setNotification({type: "success", message:"Користувача видалено!"});
                })
                .catch(() => {
                    setIsLoading(false);
                    setNotification({type: "error", message:"Щось пішло не так!"});
                });
        }

    }
    const getUsers = (page?: number) => {
        setIsLoading(true);
        const url = page ? `/users/active?page=${page}` : `/users/active`;
        axiosClient.get(url)
            .then(({data}) => {
                setIsLoading(false);
                setUsers(data.data);
                const paginationData = {...data};
                delete paginationData.data;
                setPaginationData(paginationData);
            })
            .catch(() => {
                setIsLoading(false);
            })
    }
    const changePage = (page: number) => {
        getUsers(page);
    }
    return (
        <div className="container mx-auto">
            {isLoading ?
                <div className="w-screen h-screen flex justify-center items-center bg-gray-200">
                    <div className="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-purple-500"></div>
                </div>
                :
                <div className="bg-white rounded-lg shadow-md p-6">
                    <div className="flex justify-between mb-4">
                        <h2 className="text-2xl font-bold">Користувачі</h2>
                        <button
                            className="bg-blue-500 text-white px-4 py-2 rounded-md"
                            onClick={handleOpenModal}
                        >
                            Додати користувача
                        </button>
                    </div>
                    <div className="container mx-auto mt-10">

                        <Modal isOpen={isModalOpen} onClose={handleCloseModal}>
                            <h2 className="text-xl font-bold">Модальное окно</h2>
                            <p className="mt-4">Это контент модального окна.</p>
                        </Modal>
                    </div>
                    <div className="overflow-x-auto">
                        {users.length === 0
                            ?
                            <p>Тут поки що немає нічого </p>
                            :
                            <table className="min-w-full bg-white">
                                <thead>
                                <tr>
                                    <th className="py-2 px-4 border-b">
                                        <div className="flex items-center justify-between">
                                            <span>ПІБ</span>
                                            <button className="text-gray-500 hover:text-gray-700">
                                                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M5 9l5-5 5 5H5z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                    <th className="py-2 px-4 border-b">
                                        <div className="flex items-center justify-between">
                                            <span>Email</span>
                                            <button className="text-gray-500 hover:text-gray-700">
                                                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M5 9l5-5 5 5H5z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                    <th className="py-2 px-4 border-b">
                                        <div className="flex items-center justify-between">
                                            <span>Адреса</span>
                                            <button className="text-gray-500 hover:text-gray-700">
                                                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M5 9l5-5 5 5H5z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                    <th className="py-2 px-4 border-b">
                                        <div className="flex items-center justify-between">
                                            <span>Категорія</span>
                                        </div>
                                    </th>
                                    <th className="py-2 px-4 border-b"></th>
                                </tr>
                                </thead>
                                <tbody>

                                {users.map((user) => {
                                        return (
                                            <tr key={user?.user_id} className="hover:bg-gray-100">
                                                <td className="py-2 px-4 border-b">{user?.last_name ?? ''} {user?.first_name ?? ''} {user?.patronymic_name ?? ''}</td>
                                                <td className="py-2 px-4 border-b">{user?.email}</td>
                                                <td className="py-2 px-4 border-b">{user?.city ?? ''} {user?.street ?? ''} {user?.house_number ?? ''} {user?.apartment_number ?? ''}</td>
                                                <td className="py-2 px-4 border-b">{user?.user_category === "employee" ? "співробітники" : (user?.user_category === "parent" ? "батьки" : (user?.user_category === "children" ? "діти" : "адмін. персонал"))}</td>
                                                <td className="py-2 px-4 border-b">
                                                    <button className="text-blue-500 hover:text-blue-700 mr-2"
                                                            onClick={handleOpenModal}
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                             viewBox="0 0 24 24"
                                                             strokeWidth={1.5} stroke="currentColor" className="size-6">
                                                            <path strokeLinecap="round" strokeLinejoin="round"
                                                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                                        </svg>
                                                    </button>
                                                    <button className="text-red-500 hover:text-red-700"
                                                            onClick={() => deleteUser(user?.user_id)}
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                             viewBox="0 0 24 24"
                                                             strokeWidth={1.5} stroke="currentColor" className="size-6">
                                                            <path strokeLinecap="round" strokeLinejoin="round"
                                                                  d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                        </svg>

                                                    </button>
                                                    <button className="text-orange-500 hover:text-red-700"
                                                            onClick={() => deactivateUser(user?.user_id)}
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                             viewBox="0 0 24 24"
                                                             strokeWidth={1.5} stroke="currentColor" className="size-6">
                                                            <path strokeLinecap="round" strokeLinejoin="round"
                                                                  d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                        </svg>

                                                    </button>
                                                </td>
                                            </tr>
                                        )
                                    }
                                )}

                                </tbody>
                            </table>
                        }

                    </div>
                    <Pagination currentPage={paginationData?.current_page}
                                lastPage={paginationData?.last_page}
                                from={paginationData?.from}
                                to={paginationData?.to}
                                total={paginationData?.total}
                                links={paginationData?.links}
                                onUpdatePage={changePage}></Pagination>


                </div>
            }
        </div>
    );
};

export default Users;
