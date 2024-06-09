import Breadcrumbs from "../components/Breadcrumbs.tsx";

const UserProfile = () => {
    const bredcrumpsRoutes = [{path: '/profile', displayName: "Профіль"}];
    return (
        <div className="container mx-auto">
            <Breadcrumbs routes={bredcrumpsRoutes}/>
            Profile
        </div>
    );
};

export default UserProfile;
