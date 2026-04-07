import React from 'react';
import { Link, useLocation } from 'react-router-dom';

const Sidebar: React.FC = () => {
  const location = useLocation();

  const navItems = [
    { path: '/', label: 'Dashboard', icon: '📊' },
    { path: '/equipment', label: 'Equipos', icon: '⚙️' },
    { path: '/reservations', label: 'Reservas', icon: '📅' },
    { path: '/reports', label: 'Reportes', icon: '📄' },
    { path: '/users', label: 'Usuarios', icon: '👥' },
  ];

  return (
    <aside className="w-64 bg-gray-800 text-white flex flex-col">
      <div className="p-6 border-b border-gray-700">
        <h2 className="text-xl font-bold">LabNova</h2>
      </div>
      <nav className="flex-1 p-4">
        {navItems.map((item) => (
          <Link
            key={item.path}
            to={item.path}
            className={`block px-4 py-3 rounded mb-2 ${
              location.pathname === item.path
                ? 'bg-blue-600 text-white'
                : 'text-gray-300 hover:bg-gray-700'
            }`}
          >
            <span className="mr-2">{item.icon}</span>
            {item.label}
          </Link>
        ))}
      </nav>
    </aside>
  );
};

export default Sidebar;
