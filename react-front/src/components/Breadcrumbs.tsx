import { Link } from "react-router-dom";
import { ChevronDoubleRightIcon } from '@heroicons/react/24/outline';

interface Route {
    path: string;
    displayName: string;
    exactPath?: boolean;
}

interface BreadcrumbsProps {
    routes: Route[];
}

const Breadcrumbs = ({routes}: BreadcrumbsProps) => {

    return (
        <div className="p-4 bg-white shadow-blue-500 mb-4">
            <ul className="flex space-x-2">
                <li>
                    <Link to="/" className="text-blue-600 hover:text-blue-800">Головна</Link>
                </li>
                {routes.map((route, index) => {
                    const isLast = index === routes.length - 1;
                    return (
                        <li className="inline-flex items-center" key={route.path}>
                            <ChevronDoubleRightIcon className="w-4 text-gray-400"/>
                            {isLast ?
                                <span className="ml-2 text-gray-500">{route.displayName}</span>
                                :
                                <Link to={route.path} className="ml-2 text-blue-600 hover:text-blue-800">{route.displayName}</Link>
                            }
                        </li>
                    );
                })}
            </ul>
        </div>
    );
};

export default Breadcrumbs;
