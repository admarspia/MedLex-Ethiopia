import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Activity, User, LogIn, Calendar, Package } from 'lucide-react';

function Navbar() {
  const { user, logout } = useAuth();

  return (
    <nav className="navbar">
      <div className="container" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', height: '100%' }}>
        <Link to="/" className="nav-logo" style={{ fontSize: '1.5rem', fontWeight: 900, letterSpacing: '-0.02em', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
          <Activity size={28} color="var(--color-primary)" />
          MEDLEX <span style={{ color: 'var(--text-main)' }}>ETHIOPIA</span>
        </Link>

        <div className="nav-links" style={{ display: 'flex', alignItems: 'center', gap: '2rem', flexWrap: 'wrap' }}>
          <Link to="/medicines" className="nav-link">MEDICINES</Link>
          <Link to="/pharmacies" className="nav-link">PHARMACIES</Link>

          <Link to="/services" className="nav-link">SERVICES</Link>
          <Link to="/about" className="nav-link">ABOUT</Link>
          <Link to="/contact" className="nav-link">CONTACT</Link>
          
          <div style={{ height: '30px', width: '1px', background: 'rgba(0,0,0,0.1)' }}></div>
          
          {user ? (
            <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
              <Link to="/pharmacy-dashboard" className="btn btn-primary" style={{ padding: '0.6rem 1.2rem', fontSize: '0.85rem' }}>
                <Package size={16} /> DASHBOARD
              </Link>
              <button onClick={logout} className="btn-outline" style={{ border: 'none', background: 'transparent', cursor: 'pointer', fontSize: '0.8rem', fontWeight: 600 }}>
                LOGOUT
              </button>
            </div>
          ) : (
            <Link to="/login" className="btn btn-primary" style={{ padding: '0.6rem 1.2rem', fontSize: '0.85rem' }}>
              <LogIn size={16} /> LOGIN
            </Link>
          )}
        </div>
      </div>
    </nav>
  );
}

export default Navbar;
