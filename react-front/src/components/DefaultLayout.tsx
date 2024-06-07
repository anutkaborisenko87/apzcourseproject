import {Link, Navigate, Outlet} from "react-router-dom";
import {useStateContext} from "../../contexts/ContextProvider.tsx";
const DefaultLayout = () => {
    const {user, token} = useStateContext();
    if (!token) {
        return <Navigate to="/login"/>
    }
    return (
        <div className="relative">
            <div className="h-screen flex bg-gray-100">
                {/* Sidebar */}
                <div
                    className="bg-gray-900 text-white w-64 space-y-6 py-7 px-2 absolute left-0 top-0 bottom-0 flex flex-col justify-between">
                    <div>
                        <a href="#" className="text-white flex items-center space-x-2 px-4">
                            <svg className="w-8 h-8" fill="white">
                                <path id="logo" fillRule="evenodd" d="..."/>
                            </svg>
                            <span className="text-2xl font-extrabold">Your Logo</span>
                        </a>
                        <nav>
                            <Link to="/users"
                                  className="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Користувачі</Link>
                        </nav>
                    </div>
                    <Link to="#" className="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="h-6 w-6">
                            <path fillRule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clipRule="evenodd"/>
                        </svg>
                        Вийти
                    </Link>
                </div>
                <div className="flex-shrink-0 w-64"></div>
                <div className="flex-1 flex flex-col">
                    <header className="flex justify-between items-center p-6">
                        <div className="items-center text-gray-500">
                            <span className="text-xl font-semibold"></span>
                        </div>
                        <div className="items-center text-gray-500">
                            <span className="text-xl font-semibold">{user.name}</span>
                        </div>
                    </header>
                    <main className="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
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
