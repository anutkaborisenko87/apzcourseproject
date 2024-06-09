type NotifProperty = {
    type: string;
    message: string
}

const NotificationComponent = ({type, message}: NotifProperty) => {
    return (
            type === 'success' ?
            <div
                className="flex items-center bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                role="alert">
                <strong className="font-bold">Успішно!</strong>
                <span className="block sm:inline ml-2">{message}</span>
            </div> :
            <div className="flex items-center bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                 role="alert">
                <strong className="font-bold">Ошибка!</strong>
                <span className="block sm:inline ml-2">{message}</span>
            </div>
    )
};

export default NotificationComponent;
