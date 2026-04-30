import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import { LanguageProvider } from './context/LanguageContext';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import Home from './pages/Home';
import Login from './pages/Login';
import Register from './pages/Register';
import Pharmacies from './pages/Pharmacies';
import Medicines from './pages/Medicines';
import PharmacyDashboard from './pages/PharmacyDashboard';
import MedicineDetail from './pages/MedicineDetail';
import ReserveMedicine from './pages/ReserveMedicine';
import About from './pages/About';
import Services from './pages/Services';
import Contact from './pages/Contact';

function App() {
  return (
    <LanguageProvider>
      <AuthProvider>
        <BrowserRouter>
          <div className="app-container">
            <Navbar />
            <main>
              <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/login" element={<Login />} />
                <Route path="/register" element={<Register />} />
                <Route path="/pharmacies" element={<Pharmacies />} />
                <Route path="/medicines" element={<Medicines />} />
                <Route path="/pharmacy-dashboard" element={<PharmacyDashboard />} />
                <Route path="/medicine/:id" element={<MedicineDetail />} />
                <Route path="/reserve/:id" element={<ReserveMedicine />} />
                <Route path="/about" element={<About />} />
                <Route path="/services" element={<Services />} />
                <Route path="/contact" element={<Contact />} />
              </Routes>
            </main>
            <Footer />
          </div>
        </BrowserRouter>
      </AuthProvider>
    </LanguageProvider>
  );
}

export default App;
