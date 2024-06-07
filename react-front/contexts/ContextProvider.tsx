import {createContext, ReactNode, useContext, useState} from "react";
type UserType = {
    // id: number;
    name: string;
    // другие свойства...
}
type StateContextType = {
    currentUser: UserType | null,
    token: string | null,
    setUser: (value: unknown) => void,
    setToken: (value: unknown) => void
}
const StateContext = createContext<StateContextType>({
    currentUser: null,
    token: null,
    setUser: () => {},
    setToken: () => {}
});


export const ContextProvider = ({children}: { children: ReactNode }) => {
    const [user, setUser] = useState<UserType | null>({name: 'Test'});
    const [token, _setToken] = useState(localStorage.getItem('react_front_access_token'));
    const setToken = (token: string) => {
        _setToken(token);
        if (token) {
            localStorage.setItem('react_front_access_token', token);
        } else {
            localStorage.removeItem('react_front_access_token');
        }

    }
    return (
        <StateContext.Provider value={{
            user,
            token,
            setUser,
            setToken

        }}>
            {children}
        </StateContext.Provider>
    )
};

// eslint-disable-next-line react-refresh/only-export-components
export const useStateContext = () => useContext(StateContext);
