import {createBrowserRouter} from "react-router-dom";
import Login from "./views/Login.tsx";
import Users from "./views/Users.tsx";
import NotFound from "./views/NotFound.tsx";
import DefaultLayout from "./components/DefaultLayout.tsx";
import GuestLayout from "./components/GuestLayout.tsx";
import Dashboard from "./views/Dashboard.tsx";
import UserProfile from "./views/UserProfile.tsx";
import Employees from "./views/Employees.tsx";

const router = createBrowserRouter([
    {
        path: '/',
        element: <DefaultLayout/>,
        children: [
            {
                path: '',
                element: <Dashboard/>
            },
            {
                path: 'profile',
                element: <UserProfile/>
            },
            {
                path: 'users',
                element: <Users/>
            },
            {
                path: '/employees',
                element: <Employees/>
            },
        ]
    },
    {
        path: '/',
        element: <GuestLayout/>,
        children: [
            {
                path: 'login',
                element: <Login/>
            },
        ]
    },


    {
        path: '*',
        element: <NotFound/>
    },
]);

export default router;
