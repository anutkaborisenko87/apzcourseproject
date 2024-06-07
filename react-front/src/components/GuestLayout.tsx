import {Navigate, Outlet} from "react-router-dom";
import {useStateContext} from "../../contexts/ContextProvider.tsx";

const GuestLayout = () => {
    const {token} = useStateContext();
    if (token) {
        return <Navigate to="/"/>
    }
    return (
        <div className="bg-beige min-h-screen flex items-center justify-center">
            <div className="bg-white p-6 rounded-md shadow-md">
                <Outlet/>
            </div>
        </div>
    );
};

export default GuestLayout;
