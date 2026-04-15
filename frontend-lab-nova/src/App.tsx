import { BrowserRouter as Router, Routes, Route } from 'react-router-dom'
import Layout from './components/Layout/Layout'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import Reservations from './pages/Reservations'
import Equipment from './pages/Equipment'
import Reports from './pages/Reports'
import Users from './pages/Users'
import Profile from './pages/Profile'
import { ProtectedRoute } from './components/ProtectedRoute'

function App() {
  return (
    <Router future={{ v7_startTransition: true, v7_relativeSplatPath: true }}>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route element={<Layout />}>
          <Route 
            path="/" 
            element={
              <ProtectedRoute>
                <Dashboard />
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/reservations" 
            element={
              <ProtectedRoute requiredModule="reservations">
                <Reservations />
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/equipment" 
            element={
              <ProtectedRoute requiredModule="equipment">
                <Equipment />
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/reports" 
            element={
              <ProtectedRoute requiredModule="reports">
                <Reports />
              </ProtectedRoute>
            } 
          />
          <Route
            path="/users"
            element={
              <ProtectedRoute requiredModule="users">
                <Users />
              </ProtectedRoute>
            }
          />
          <Route
            path="/profile"
            element={
              <ProtectedRoute>
                <Profile />
              </ProtectedRoute>
            }
          />
        </Route>
      </Routes>
    </Router>
  )
}

export default App
