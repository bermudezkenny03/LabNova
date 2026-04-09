import React from 'react';
import { Link, useLocation } from 'react-router-dom';

const navItems = [
  { path: '/',             label: 'Dashboard', icon: '📊' },
  { path: '/equipment',   label: 'Equipos',    icon: '🔬' },
  { path: '/reservations',label: 'Reservas',   icon: '📅' },
  { path: '/reports',     label: 'Reportes',   icon: '📄' },
  { path: '/users',       label: 'Usuarios',   icon: '👥' },
];

const Sidebar: React.FC = () => {
  const location = useLocation();

  return (
    <aside className="w-60 bg-gray-900 text-white flex flex-col shrink-0">
      {/* Logo */}
      <div className="flex flex-col items-center py-6 px-4 border-b border-gray-700">
        <img
          src="/logo.png"
          alt="LabNova"
          className="w-16 h-16 object-contain rounded-full bg-white p-1 shadow mb-2"
        />
        <span className="text-sm font-bold tracking-wide text-white">LabNova</span>
        <span className="text-xs text-gray-400 tracking-widest">Breakthrough Solutions</span>
      </div>

      {/* Navegacion */}
      <nav className="flex-1 px-3 py-4 space-y-1">
        {navItems.map((item) => {
          const active = location.pathname === item.path;
          return (
            <Link
              key={item.path}
              to={item.path}
              className={`flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors ${
                active
                  ? 'bg-blue-600 text-white'
                  : 'text-gray-300 hover:bg-gray-800 hover:text-white'
              }`}
            >
              <span className="text-base">{item.icon}</span>
              {item.label}
            </Link>
          );
        })}
      </nav>

      {/* Footer del sidebar */}
      <div className="px-4 py-3 border-t border-gray-700">
        <p className="text-xs text-gray-500 text-center">v1.0.0</p>
      </div>
    </aside>
  );
};

export default Sidebar;
