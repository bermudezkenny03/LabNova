import { BrowserRouter as Router, Routes, Route } from 'react-router-dom'
import Layout from './components/Layout/Layout'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import Reservations from './pages/Reservations'
import Equipment from './pages/Equipment'
import Reports from './pages/Reports'
import Users from './pages/Users'

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route element={<Layout />}>
          <Route path="/" element={<Dashboard />} />
          <Route path="/reservations" element={<Reservations />} />
          <Route path="/equipment" element={<Equipment />} />
          <Route path="/reports" element={<Reports />} />
          <Route path="/users" element={<Users />} />
        </Route>
      </Routes>
    </Router>
  )
}

export default App
