import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';

// Pages
import Home from './pages/Home';
import Medicines from './pages/Medicines';
import MedicineDetail from './pages/MedicineDetail';
import Pharmacies from './pages/Pharmacies';
import PharmacyDashboard from './pages/PharmacyDashboard';
import Login from './pages/Login';
import Register from './pages/Register';
import About from './pages/About';
import Contact from './pages/Contact';
import Services from './pages/Services';
import ReserveMedicine from './pages/ReserveMedicine';

// Components
import Navbar from './components/Navbar';
import Footer from './components/Footer';

// Protected Route Component
const ProtectedRoute = ({ children }) => {
  const { user } = useAuth();
  return user ? children : <Navigate to="/login" />;
};

function App() {
  return (
    <Router>
      <AuthProvider>
        <div style={{ display: 'flex', flexDirection: 'column', minHeight: '100vh' }}>
          <Navbar />
          
          <main style={{ flex: 1 }}>
            <Routes>
              <Route path="/" element={<Home />} />
              <Route path="/medicines" element={<Medicines />} />
              <Route path="/medicines/:id" element={<MedicineDetail />} />
              <Route path="/pharmacies" element={<Pharmacies />} />
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Register />} />
              <Route path="/about" element={<About />} />
              <Route path="/contact" element={<Contact />} />
              <Route path="/services" element={<Services />} />

              <Route 
                path="/reserve/:id" 
                element={<ReserveMedicine />} 
              />

              <Route 
                path="/pharmacy-dashboard"
                element={
                  <ProtectedRoute>
                    <PharmacyDashboard />
                  </ProtectedRoute>
                }
              />
            </Routes>
          </main>

          <Footer />
        </div>
      </AuthProvider>
    </Router>
  );
}

export default App;
