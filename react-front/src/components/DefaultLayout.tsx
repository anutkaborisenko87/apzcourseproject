import {NavLink, Navigate, Outlet} from "react-router-dom";
import {useStateContext} from "../../contexts/ContextProvider.tsx";
import axiosClient from "../axios-client.ts";
import {useEffect} from "react";
import NotificationComponent from "./NotificationComponent.tsx";
import { UserGroupIcon, UserCircleIcon, ArrowUturnRightIcon, WalletIcon } from '@heroicons/react/24/outline';
const DefaultLayout = () => {
    function  classNames (...classes) {
        return classes.filter(Boolean).join(' ');
    }
    const {user, token, notification, setToken, setUser} = useStateContext();
    if (!token) {
        return <Navigate to="/login"/>
    }
    const logout = () => {
        axiosClient.get('/logout')
            .then(() => {
                setToken(null);
                setUser(null);
            })
            .catch(error => {
                console.log(error.response);
            })
    }
    // eslint-disable-next-line react-hooks/rules-of-hooks
    useEffect(() => {
        axiosClient.get('/logged_user')
            .then(({data}) => {
                setUser(data?.user);
            })
            .catch(error => {
                setToken(null);
                setUser(null);
                console.error(error)
            })
    }, []);
    return (
        <div className="relative">
            <div className="h-screen flex bg-gray-100">
                {/* Sidebar */}
                <div
                    className="bg-gray-900 text-white w-64 space-y-6 py-2 px-2 absolute left-0 top-0 bottom-0 flex flex-col justify-between">
                    <div>
                        <NavLink to="/" className="text-white flex items-center space-x-2 px-4 mb-4">
                            <svg className="w-8 h-8" fill="white">
                                <path id="logo" fillRule="evenodd" d="..."/>
                            </svg>
                            <span className="text-lg text-center font-extrabold">ІС "Центр дошкільного розвитку"</span>
                        </NavLink>
                        <nav>
                            <NavLink to="/users"
                                     className={ ({ isActive }) => classNames(`flex py-2.5 px-4 rounded transition duration-200 ${isActive ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white'}`)}>
                                <UserGroupIcon className="w-6 mr-2"/>
                                Користувачі
                            </NavLink>
                            <NavLink to="/employees"
                                     className={ ({ isActive }) => classNames(`flex py-2.5 px-4 rounded transition duration-200 ${isActive ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white'}`)}>
                                <WalletIcon className="w-6 mr-2"/>
                                Співробітники
                            </NavLink>
                        </nav>
                    </div>
                    <NavLink to="#" onClick={logout}
                          className="flex py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white items-center space-x-2">
                        <ArrowUturnRightIcon className="w-6 mr-2"/>

                        Вийти
                    </NavLink>
                </div>
                <div className="flex-shrink-0 w-64"></div>
                <div className="flex-1 flex flex-col">
                    <header className="flex justify-between items-center p-6">
                        <div className="items-center text-gray-500">
                            <span className="text-xl font-semibold"></span>
                        </div>
                        <NavLink to="/profile" className="flex items-center text-gray-500">
                            <UserCircleIcon className="w-6 mr-2"/>
                            <span className="text-xl font-semibold">{user?.name}</span>
                        </NavLink>
                    </header>
                    <main className="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                        {
                            notification.message !== ''
                            ? <NotificationComponent type={notification.type} message={notification.message}/> :<></>
                        }

                        <div className="container mx-auto px-6 py-8">
                            <Outlet/>
                        </div>
                    </main>
                </div>
            </div>
        </div>

    );
};

export default DefaultLayout;
