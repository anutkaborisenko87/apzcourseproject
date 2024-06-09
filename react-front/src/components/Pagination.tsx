import {useEffect, useState} from "react";
import {Link} from "react-router-dom";

type PaginationProps = {
    currentPage: number,
    lastPage: number,
    from: number,
    to: number,
    total: number,
    links: Array<any>,
    onUpdatePage: any
}
const Pagination = ({currentPage, from, to, total, lastPage, links, onUpdatePage}: PaginationProps) => {
    const [currentPageValue, setCurrentPageValue] = useState(currentPage);
    const previousPage = () => {
        setCurrentPageValue(currentPageValue => currentPageValue - 1)
    }
    const nextPage = () => {
        setCurrentPageValue(currentPageValue => currentPageValue + 1)
    }
    const selectPage = (page: number) => {
        setCurrentPageValue(page)
    }
    useEffect(() => {
        if (currentPageValue !== currentPage) {
            onUpdatePage(currentPageValue);
        }
    }, [currentPageValue, currentPage, onUpdatePage]);
    return (
        links.length > 3
            ?
            <div
                className="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                <div className="flex flex-1 justify-between sm:hidden">
                    <a href="#"
                       className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
                    <a href="#"
                       className="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
                </div>
                <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p className="text-sm text-gray-700">
                            Показано від
                            <span className="font-medium"> {from} </span>
                            до
                            <span className="font-medium"> {to} </span>
                            з
                            <span className="font-medium">{total}</span>
                        </p>
                    </div>
                    <div>
                        <nav className="isolate inline-flex -space-x-px rounded-md shadow-sm"
                             aria-label="Pagination">
                            <button
                                onClick={previousPage}
                                className="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                disabled={currentPageValue <= 1}
                            >
                                <span className="sr-only"></span>
                                <svg className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                     aria-hidden="true">
                                    <path fillRule="evenodd"
                                          d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                                          clipRule="evenodd"/>
                                </svg>
                            </button>
                            {links.slice(1, -1).map((item, index) => (
                                <Link key={index} to="#"
                                      className={item.active ?
                                          "relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                          : "relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                      }
                                      onClick={() => selectPage(parseInt(item.label))}
                                >{item.label}</Link>
                            ))}
                            <button
                                onClick={nextPage}
                                className="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                disabled={currentPageValue >= lastPage}
                            >
                                <span className="sr-only">Next</span>
                                <svg className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                     aria-hidden="true">
                                    <path fillRule="evenodd"
                                          d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                          clipRule="evenodd"/>
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
            :
            <></>
    );
};

export default Pagination;
