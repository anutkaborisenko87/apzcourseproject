import React from 'react';
import Breadcrumbs from "../components/Breadcrumbs.tsx";

const Employees = () => {
    const bredcrumpsRoutes = [{path: '/employees', displayName: "Співробітники"}];
    return (
        <div className="container mx-auto">
            <Breadcrumbs routes={bredcrumpsRoutes}/>
        </div>
    );
};

export default Employees;
