import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Navbar() {
  const { user, logout } = useAuth();
  return (
    <nav className="navbar">
      <div className="container navbar-content">
        <Link to="/" className="navbar-brand">
          <span>MedLex</span> Ethiopia
        </Link>
        <div className="nav-links">
          <Link to="/" className="nav-item">Home</Link>
          <Link to="/pharmacies" className="nav-item">Pharmacies</Link>
          <Link to="/medicines" className="nav-item">Medicines</Link>
          {user ? (
            <>
              <Link to="/pharmacy-dashboard" className="nav-item">Dashboard</Link>
              <button className="btn btn-outline" onClick={logout} style={{ padding: '0.4rem 1rem' }}>Logout</button>
            </>
          ) : (
            <Link to="/login" className="btn btn-primary" style={{ padding: '0.5rem 1.25rem' }}>Login</Link>
          )}
        </div>
      </div>
    </nav>
  );
}

export default Navbar;
