import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { UserPlus, Upload, ShieldCheck, Mail, Phone, MapPin, Lock } from 'lucide-react';

function Register() {
  const [formData, setFormData] = useState({
    name: '',
    address: '',
    phone: '',
    email: '',
    password: ''
  });
  const [licenseFile, setLicenseFile] = useState(null);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();
  const { register } = useAuth();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (formData.password.length < 6) {
      setError('Password must be at least 6 characters long.');
      return;
    }
    if (!licenseFile) {
      setError('Please upload your pharmacy license for verification.');
      return;
    }

    setLoading(true);
    const submitData = new FormData();
    submitData.append('name', formData.name);
    submitData.append('address', formData.address);
    submitData.append('phone', formData.phone);
    submitData.append('email', formData.email);
    submitData.append('password', formData.password);
    submitData.append('license', licenseFile);

    const result = await register(submitData);
    
    if (result.success) {
      navigate('/pharmacy-dashboard');
    } else {
      setError(result.message || 'Registration failed. Please try again.');
    }
    setLoading(false);
  };

  return (
    <div className="container" style={{ padding: '4rem 0', display: 'flex', justifyContent: 'center' }}>
      <div className="card" style={{ width: '100%', maxWidth: '650px', padding: '2.5rem' }}>
        <div style={{ textAlign: 'center', marginBottom: '2rem' }}>
          <div style={{ width: '64px', height: '64px', borderRadius: '12px', background: 'var(--color-primary)', color: '#fff', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', marginBottom: '1rem' }}>
            <UserPlus size={32} />
          </div>
          <h2 style={{ fontSize: '1.8rem', marginBottom: '0.5rem' }}>Pharmacy Network Registration</h2>
          <p style={{ color: 'var(--text-muted)' }}>Complete verification to join the Ethiopian medication network</p>
        </div>

        {error && (
          <div style={{ padding: '0.75rem', marginBottom: '1.5rem', backgroundColor: 'var(--color-primary-light)', color: 'var(--color-primary)', borderRadius: '8px', fontSize: '0.85rem', textAlign: 'center' }}>
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: '1rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600, fontSize: '0.9rem' }}>Official Pharmacy Name</label>
            <input
              type="text"
              placeholder="e.g., Life-Line Pharmacy Addis"
              required
              className="search-input"
              value={formData.name}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
              style={{ width: '100%' }}
            />
          </div>
          
          <div style={{ marginBottom: '1rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600, fontSize: '0.9rem' }}>Physical Address</label>
            <div style={{ position: 'relative' }}>
              <MapPin size={18} style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)', color: 'var(--text-muted)' }} />
              <input
                type="text"
                placeholder="Street, Woreda, City"
                required
                className="search-input"
                value={formData.address}
                onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                style={{ paddingLeft: '3rem', width: '100%' }}
              />
            </div>
          </div>
          
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1rem' }}>
            <div>
              <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600, fontSize: '0.9rem' }}>Phone Number</label>
              <div style={{ position: 'relative' }}>
                <Phone size={18} style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)', color: 'var(--text-muted)' }} />
                <input
                  type="tel"
                  placeholder="+251 911 222 333"
                  required
                  className="search-input"
                  value={formData.phone}
                  onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                  style={{ paddingLeft: '3rem', width: '100%' }}
                />
              </div>
            </div>
            <div>
              <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600, fontSize: '0.9rem' }}>Email Address</label>
              <div style={{ position: 'relative' }}>
                <Mail size={18} style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)', color: 'var(--text-muted)' }} />
                <input
                  type="email"
                  placeholder="admin@pharmacy.com"
                  required
                  className="search-input"
                  value={formData.email}
                  onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                  style={{ paddingLeft: '3rem', width: '100%' }}
                />
              </div>
            </div>
          </div>
          
          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: 600, fontSize: '0.9rem' }}>Account Password</label>
            <div style={{ position: 'relative' }}>
              <Lock size={18} style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)', color: 'var(--text-muted)' }} />
              <input
                type="password"
                placeholder="Min. 6 characters"
                required
                minLength="6"
                className="search-input"
                value={formData.password}
                onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                style={{ paddingLeft: '3rem', width: '100%' }}
              />
            </div>
          </div>

          <div style={{ background: 'rgba(0,0,0,0.02)', padding: '2rem', borderRadius: '12px', border: '2px dashed #ddd', textAlign: 'center', marginBottom: '1.5rem' }}>
            <Upload size={32} style={{ color: 'var(--color-primary)', marginBottom: '0.5rem' }} />
            <h4 style={{ marginBottom: '0.5rem' }}>Professional License</h4>
            <p style={{ fontSize: '0.8rem', color: 'var(--text-muted)', marginBottom: '1rem' }}>Upload your pharmacy license (PDF/JPG/PNG) for verification</p>
            <input
              type="file"
              accept=".pdf,.jpg,.jpeg,.png"
              required
              className="search-input"
              onChange={(e) => setLicenseFile(e.target.files[0])}
              style={{ background: '#fff', width: '100%' }}
            />
          </div>

          <button type="submit" disabled={loading} className="btn btn-primary" style={{ width: '100%', padding: '1rem', fontSize: '1rem' }}>
            {loading ? 'Processing Application...' : 'Create Professional Account'}
          </button>

          <div style={{ marginTop: '1rem', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '0.5rem', fontSize: '0.8rem', color: 'var(--text-muted)' }}>
            <ShieldCheck size={14} /> Data encrypted and stored following ET health regulations.
          </div>

          <p style={{ textAlign: 'center', marginTop: '1.5rem', color: 'var(--text-muted)', fontSize: '0.9rem' }}>
            Already part of the network? <Link to="/login" style={{ color: 'var(--color-primary)', fontWeight: 700 }}>Sign In</Link>
          </p>
        </form>
      </div>
    </div>
  );
}

export default Register;
