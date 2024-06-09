import axiosClient from "../axios-client.ts";
import {useStateContext} from "../../contexts/ContextProvider.tsx";
import {useEffect, useState} from "react";

type UserType = {
    id: number,
}

type FormProps = {
    user?: UserType,
    onCloseModal: () => void
    onSubmitForm: () => void
}
export default function AddUpdateUserForm({user, onCloseModal, onSubmitForm}: FormProps) {
    const {setNotification} = useStateContext();
    const [roles, setRoles] = useState([]);
    const today = new Date();
    const maxDate = `${today.getFullYear()}-${(today.getMonth() + 1).toString().padStart(2, '0')}-${today.getDate().toString().padStart(2, '0')}`;
    const [errors, setErrors] = useState({
        first_name: [],
        last_name: [],
        patronymic_name: [],
        email: [],
        role: [],
        city: [],
        street: [],
        house_number: [],
        apartment_number: [],
        birth_date: []
    });
    const [allertError, setAllertError] = useState('');

    const [userFormData, setUserFormData] = useState({
        first_name: '',
        last_name: '',
        patronymic_name: '',
        email: '',
        role: '',
        city: '',
        street: '',
        house_number: '',
        apartment_number: '',
        birth_date: '',
        user_id: ''
    });
    const clearErrors = (field: string) => {
        setAllertError('');
        setErrors({ ...errors, [field]: [] });
    };
    const safeValue = (value, defaultValue = '') => value !== null ? value : defaultValue;
    const getUser = () => {
        axiosClient.get(`/user/${user?.id}`)
            .then(({data}) => {
                setUserFormData({
                    first_name: safeValue(data.first_name),
                    last_name: safeValue(data.last_name),
                    patronymic_name: safeValue(data.patronymic_name),
                    email: safeValue(data.email),
                    role: safeValue(data.role),
                    city: safeValue(data.city),
                    street: safeValue(data.street),
                    house_number: safeValue(data.house_number),
                    apartment_number: safeValue(data.apartment_number),
                    birth_date: safeValue(data.birth_date),
                    user_id: safeValue(data.user_id)
                });
            })
            .catch((error) => {
                setNotification({type: "error", message: "Щось пішло не так!"});
                console.error(error)
            })

    }
    const getRoles = () => {
        axiosClient.get(`/roles_list`)
            .then(({data}) => {
                setRoles(data);
            })
            .catch((error) => {
                setNotification({type: "error", message: "Щось пішло не так!"});
                console.error(error)
            })

    }

    const submitForm = (ev) => {
        ev.preventDefault();
        if (!user) {
            createUser();
        }
    }
    const cleanObject = (object) => {
        return Object.fromEntries(
            Object.entries(object).filter(([_, v]) => v != null && v !== '')
        );
    }
    const createUser = () => {
        const cleanedFormData = cleanObject(userFormData);
        axiosClient.post('/users/create', cleanedFormData)
            .then(({data}) => {
                onSubmitForm();
                let userName = `${data.last_name} ${data.first_name}  ${data.patronymic_name ?? ''}`;
                let category = data?.user_category === "employee" ? "співробітники" : (data?.user_category === "parent" ? "батьки" : (data?.user_category === "children" ? "діти" : "адмін. персонал"));
                setNotification({type: "success", message:`Користувача ${userName} додано до категорії ${category}!`});
                console.log(data)
            })
            .catch(({response}) => {
                if (response && response.status === 422) {
                    const {
                        first_name,
                        last_name,
                        patronymic_name,
                        email,
                        role,
                        city,
                        street,
                        house_number,
                        apartment_number,
                        birth_date
                    } = response.data.errors;
                    setErrors(prevErrors => ({
                        ...prevErrors,
                        ...(first_name ? {first_name} : {}),
                        ...(last_name ? {last_name} : {}),
                        ...(patronymic_name ? {patronymic_name} : {}),
                        ...(email ? {email} : {}),
                        ...(role ? {role} : {}),
                        ...(city ? {city} : {}),
                        ...(street ? {street} : {}),
                        ...(house_number ? {house_number} : {}),
                        ...(apartment_number ? {apartment_number} : {}),
                        ...(birth_date ? {birth_date} : {})
                    }));
                }
                if (response && response.status !== 422) {
                    const {error} = response.data;
                    setAllertError(error);
                }
            })
    }


    useEffect(() => {
        if (user) {
            getUser();
        }
        getRoles();
    }, []);
    return (
        <form onSubmit={submitForm}>
            <div className="space-y-12">
                <div className="border-b border-gray-900/10 pb-12">
                    {
                        user?.id
                            ?
                            <>
                                <h2 className="text-base font-semibold leading-7 text-gray-900">
                                    Редагування даних користувача
                                </h2>
                                <h4 className="text-base font-bold leading-7 text-gray-900">
                                    {userFormData.last_name} {userFormData.first_name} {userFormData.patronymic_name}
                                </h4>
                            </>
                            :
                            <h2 className="text-base font-semibold leading-7 text-gray-900">
                                Створення нового користувача
                            </h2>
                    }


                    <div className="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        {allertError !== '' ?
                            <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                                <p className="font-bold">Помилка!</p>
                                <p>{allertError}</p>
                            </div>
                            : ''

                        }

                        <div className="sm:col-span-3">
                            <label htmlFor="last-name" className="block text-sm font-medium leading-6 text-gray-900">
                                Прізвище
                            </label>
                            <div className="mt-2">
                                <input
                                    type="text"
                                    name="last_name"
                                    id="last-name"
                                    value={userFormData.last_name}
                                    className={`block w-full rounded-md border-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:leading-6 ${errors.last_name?.length > 0 ? 'border-red-600 focus:border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-indigo-600'}`}
                                    onChange={e => {
                                        clearErrors('last_name');
                                        setUserFormData(userFormData => ({...userFormData, last_name: e.target.value}));
                                    }}
                                />
                            </div>
                            {errors.last_name.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.last_name.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>
                        <div className="sm:col-span-3">
                            <label htmlFor="first-name" className="block text-sm font-medium leading-6 text-gray-900">
                               Ім'я
                            </label>
                            <div className="mt-2">
                                <input
                                    type="text"
                                    name="first_name"
                                    id="first-name"
                                    value={userFormData.first_name}
                                    className={`block w-full rounded-md border-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:leading-6 ${errors.first_name?.length > 0 ? 'border-red-600 focus:border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-indigo-600'}`}
                                    onChange={e => {
                                        clearErrors('first_name');
                                        setUserFormData(userFormData => ({...userFormData, first_name: e.target.value}));
                                    }}
                                />
                            </div>
                            {errors.first_name.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.first_name.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>
                        <div className="sm:col-span-4">
                            <label htmlFor="patronymic_name" className="block text-sm font-medium leading-6 text-gray-900">
                               По батькові
                            </label>
                            <div className="mt-2">
                                <input
                                    type="text"
                                    name="patronymic_name"
                                    id="patronymic_name"
                                    value={userFormData.patronymic_name ?? ''}
                                    className={`block w-full rounded-md border-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:leading-6 ${errors.patronymic_name?.length > 0 ? 'border-red-600 focus:border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-indigo-600'}`}
                                    onChange={e => {
                                        clearErrors('patronymic_name');
                                        setUserFormData(userFormData => ({...userFormData, patronymic_name: e.target.value}));
                                    }}
                                />
                            </div>
                            {errors.patronymic_name.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.patronymic_name.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>
                        <div className="sm:col-span-4">
                            <label htmlFor="email" className="block text-sm font-medium leading-6 text-gray-900">
                                Email
                            </label>
                            <div className="mt-2">
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value={userFormData.email}
                                    className={`block w-full rounded-md border-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:leading-6 ${errors.email?.length > 0 ? 'border-red-600 focus:border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-indigo-600'}`}
                                    onChange={e => {
                                        clearErrors('email');
                                        setUserFormData(userFormData => ({...userFormData, email: e.target.value}));
                                    }}
                                />
                            </div>
                            {errors.email.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.email.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="sm:col-span-3">
                            <label htmlFor="role" className="block text-sm font-medium leading-6 text-gray-900">
                                Роль користувача у системі
                            </label>
                            <div className="mt-2">
                                <select
                                    id="role"
                                    name="role"
                                    value={userFormData?.role?.id ?? ''}
                                    onChange={e => {
                                        clearErrors('patronymic_name');
                                        setUserFormData(userFormData => ({...userFormData, role: e.target.value}));
                                    }}
                                    className="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6"
                                >

                                    <option>не назначено</option>
                                    {
                                        roles.length > 0 ?
                                            roles.map((role) => {
                                                return (
                                                    <option key={role.id} value={role.id}>{role.name}</option>
                                                );
                                            })
                                            : <></>
                                    }

                                </select>
                            </div>
                            {errors.role.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.role.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="col-span-full">
                            <label htmlFor="city"
                                   className="block text-sm font-medium leading-6 text-gray-900">
                                Населений пункт
                            </label>
                            <div className="mt-2">
                                <input
                                    type="text"
                                    name="city"
                                    id="city"
                                    value={userFormData.city}
                                    className={`block w-full rounded-md border-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:leading-6 ${errors.city?.length > 0 ? 'border-red-600 focus:border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-indigo-600'}`}
                                    onChange={e => {
                                        clearErrors('city');
                                        setUserFormData(userFormData => ({...userFormData, city: e.target.value}));
                                    }}
                                />
                            </div>
                            {errors.city.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.city.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="sm:col-span-2 sm:col-start-1">
                            <label htmlFor="street" className="block text-sm font-medium leading-6 text-gray-900">
                                Вулиця
                            </label>
                            <div className="mt-2">
                                <input
                                    type="text"
                                    name="street"
                                    id="street"
                                    value={userFormData.street}
                                    className={`block w-full rounded-md border-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:leading-6 ${errors.street?.length > 0 ? 'border-red-600 focus:border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-indigo-600'}`}
                                    onChange={e => {
                                        clearErrors('city');
                                        setUserFormData(userFormData => ({...userFormData, street: e.target.value}));
                                    }}
                                />
                            </div>
                            {errors.street.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.street.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="sm:col-span-2">
                            <label htmlFor="house_number" className="block text-sm font-medium leading-6 text-gray-900">
                                Номер будинку
                            </label>
                            <div className="mt-2">
                                <input
                                    type="text"
                                    name="house_number"
                                    id="house_number"
                                    value={userFormData.house_number}
                                    className={`block w-full rounded-md border-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:leading-6 ${errors.house_number?.length > 0 ? 'border-red-600 focus:border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-indigo-600'}`}
                                    onChange={e => {
                                        clearErrors('house_number');
                                        setUserFormData(userFormData => ({...userFormData, house_number: e.target.value}));
                                    }}
                                />
                            </div>
                            {errors.house_number.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.house_number.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="sm:col-span-2">
                            <label htmlFor="apartment_number" className="block text-sm font-medium leading-6 text-gray-900">
                                Номер квартири
                            </label>
                            <div className="mt-2">
                                <input
                                    type="text"
                                    name="apartment_number"
                                    id="apartment_number"
                                    value={userFormData.apartment_number}
                                    className={`block w-full rounded-md border-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:leading-6 ${errors.apartment_number?.length > 0 ? 'border-red-600 focus:border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-indigo-600'}`}
                                    onChange={e => {
                                        clearErrors('apartment_number');
                                        setUserFormData(userFormData => ({...userFormData, apartment_number: e.target.value}));
                                    }}
                                />
                            </div>
                            {errors.apartment_number.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.apartment_number.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="sm:col-span-2">
                            <label htmlFor="birthdate" className="block text-sm font-medium leading-6 text-gray-900">
                                Номер квартири
                            </label>
                            <div className="mt-2">
                                <input
                                    type="date"
                                    name="birthdate"
                                    max={maxDate}
                                    id="birthdate"
                                    value={userFormData.birth_date}
                                    className={`block w-full rounded-md border-1 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:leading-6 ${errors.birthdate?.length > 0 ? 'border-red-600 focus:border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-indigo-600'}`}
                                    onChange={e => {
                                        clearErrors('birth_date');
                                        setUserFormData(userFormData => ({...userFormData, birth_date: e.target.value}));
                                    }}
                                />
                            </div>
                            {errors.birth_date.length > 0 && (
                                <div className="text-red-500 text-xs">
                                    {errors.birth_date.map((error, index) => (
                                        <p key={index}>{error}</p>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>

            </div>

            <div className="mt-6 flex items-center justify-end gap-x-6">
                <button onClick={onCloseModal} type="button" className="text-sm font-semibold leading-6 text-gray-900">
                    Cancel
                </button>
                <button
                    type="submit"
                    className="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    Save
                </button>
            </div>
        </form>
    )
}
