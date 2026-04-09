import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../hooks';

const Header: React.FC = () => {
  const navigate = useNavigate();
  const { user } = useAuth();

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    navigate('/login');
  };

  return (
    <header className="bg-white border-b border-gray-200 px-6 py-3 flex justify-between items-center">
      <div className="flex items-center gap-3">
        <img src="/logo.png" alt="LabNova" className="w-8 h-8 object-contain" />
        <span className="text-lg font-bold text-gray-800">LabNova</span>
      </div>
      <div className="flex items-center gap-4">
        <div className="text-right hidden sm:block">
          <p className="text-sm font-medium text-gray-700">
            {user ? `${user.name}${(user as { last_name?: string }).last_name ? ' ' + (user as { last_name?: string }).last_name : ''}` : 'Usuario'}
          </p>
          {user?.role && (
            <p className="text-xs text-gray-400">{user.role.name}</p>
          )}
        </div>
        <button
          onClick={handleLogout}
          className="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition-colors"
        >
          Salir
        </button>
      </div>
    </header>
  );
};

export default Header;
