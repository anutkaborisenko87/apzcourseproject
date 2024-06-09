import {useEffect, useState} from "react";
import axiosClient from "../axios-client.ts";
import Pagination from "../components/Pagination.tsx";
import Modal from "../components/Modal.tsx";
import {useStateContext} from "../../contexts/ContextProvider.tsx";
import {PencilSquareIcon} from "@heroicons/react/24/outline";
import {MinusCircleIcon} from "@heroicons/react/24/outline";
import {TrashIcon} from "@heroicons/react/24/outline";
import Breadcrumbs from "../components/Breadcrumbs.tsx";
import AddUpdateUserForm from "../components/AddUpdateUserForm.tsx";

const Users = () => {
    const bredcrumpsRoutes = [{path: '/users', displayName: "Користувачі"}];
    const {setNotification} = useStateContext();
    const [users, setUsers] = useState([]);
    const [userToUpdate, setUserToUpdate] = useState(null);
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

    const handleOpenModal = (userId?: number) => {
        if (userId) setUserToUpdate({id: userId});
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setUserToUpdate(null);
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
    const onSubmitform = () => {
        getUsers();
        handleCloseModal();
    }
    return (
        <div className="container mx-auto">
            <Breadcrumbs routes={bredcrumpsRoutes}/>
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
                            onClick={() => handleOpenModal()}
                        >
                            Додати користувача
                        </button>
                    </div>
                    <div className="container mx-auto mt-10">

                        <Modal isOpen={isModalOpen} onClose={handleCloseModal}>
                            <AddUpdateUserForm user={userToUpdate} onCloseModal={handleCloseModal} onSubmitForm={onSubmitform}/>
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
                                                            onClick={() => handleOpenModal(user?.user_id)}
                                                    >
                                                        <PencilSquareIcon className="w-6"/>

                                                    </button>
                                                    <button className="text-red-500 hover:text-red-700"
                                                            onClick={() => deleteUser(user?.user_id)}
                                                    >
                                                        <TrashIcon className="w-6"/>
                                                    </button>
                                                    <button className="text-orange-500 hover:text-red-700"
                                                            onClick={() => deactivateUser(user?.user_id)}
                                                    >
                                                        <MinusCircleIcon className="w-6"/>
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
