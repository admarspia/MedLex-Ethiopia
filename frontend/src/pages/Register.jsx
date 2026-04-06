import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Register() {
    const [formData, setFormData] = useState({ name: '', address: '', phone: '', email: '', password: '' });
    const [licenseFile, setLicenseFile] = useState(null);
    const [errorMsg, setErrorMsg] = useState('');
    const [loading, setLoading] = useState(false);

    const navigate = useNavigate();
    const { register } = useAuth();

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrorMsg('');

        // Frontend Validations (Recommendation)
        if (formData.password.length < 6) {
            setErrorMsg('Password must be at least 6 characters long.');
            return;
        }
        if (!licenseFile) {
            setErrorMsg('Please upload your pharmacy license for verification.');
            return;
        }

        setLoading(true);
        const submitData = { ...formData, licenseFile };
        const res = await register(submitData);
        setLoading(false);

        if (res.success) {
            navigate('/pharmacy-dashboard');
        } else {
            setErrorMsg(res.message);
        }
    };

    return (
        <div className="container" style={{ padding: '5rem 0', display: 'flex', justifyContent: 'center' }}>
            <div className="glass-panel" style={{ width: '100%', maxWidth: '450px' }}>
                <h2 style={{ textAlign: 'center', marginBottom: '1.5rem', color: 'var(--text-main)' }}>Register Pharmacy</h2>

                {errorMsg && (
                    <div style={{ padding: '0.8rem', marginBottom: '1rem', backgroundColor: 'rgba(239, 68, 68, 0.1)', color: 'var(--color-primary-dark)', borderRadius: '8px', border: '1px solid var(--color-primary-light)', fontSize: '0.9rem' }}>
                        {errorMsg}
                    </div>
                )}

                <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
                    <div>
                        <label style={{ fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '0.3rem', display: 'block' }}>Pharmacy Name</label>
                        <input type="text" placeholder="e.g. Lion Pharmacy" required className="search-input" style={{ width: '100%', padding: '0.8rem', borderRadius: '8px' }}
                            onChange={(e) => setFormData({ ...formData, name: e.target.value })} disabled={loading} />
                    </div>
                    <div>
                        <label style={{ fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '0.3rem', display: 'block' }}>Address</label>
                        <input type="text" placeholder="Complete Address" required className="search-input" style={{ width: '100%', padding: '0.8rem', borderRadius: '8px' }}
                            onChange={(e) => setFormData({ ...formData, address: e.target.value })} disabled={loading} />
                    </div>
                    <div>
                        <label style={{ fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '0.3rem', display: 'block' }}>Phone Number</label>
                        <input type="text" placeholder="+251 9XX XXX XXX" required className="search-input" style={{ width: '100%', padding: '0.8rem', borderRadius: '8px' }}
                            onChange={(e) => setFormData({ ...formData, phone: e.target.value })} disabled={loading} />
                    </div>
                    <div>
                        <label style={{ fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '0.3rem', display: 'block' }}>Email Address</label>
                        <input type="email" placeholder="Email Address" required className="search-input" style={{ width: '100%', padding: '0.8rem', borderRadius: '8px' }}
                            onChange={(e) => setFormData({ ...formData, email: e.target.value })} disabled={loading} />
                    </div>
                    <div>
                        <label style={{ fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '0.3rem', display: 'block' }}>Password</label>
                        <input type="password" placeholder="Min 6 characters" required minLength="6" className="search-input" style={{ width: '100%', padding: '0.8rem', borderRadius: '8px' }}
                            onChange={(e) => setFormData({ ...formData, password: e.target.value })} disabled={loading} />
                    </div>

                    {/* License Upload Section */}
                    <div>
                        <label style={{ fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '0.3rem', display: 'block' }}>Upload Pharmacy License (PDF/JPG/PNG)</label>
                        <input type="file" accept=".pdf, .jpg, .jpeg, .png" required className="search-input" style={{ width: '100%', padding: '0.8rem', borderRadius: '8px', fontSize: '0.9rem', cursor: 'pointer' }}
                            onChange={(e) => setLicenseFile(e.target.files[0])} disabled={loading} />
                        <small style={{ color: 'var(--text-muted)', fontSize: '0.75rem', marginTop: '0.2rem', display: 'block' }}>Max size 5MB.</small>
                    </div>

                    <button type="submit" disabled={loading} className="btn btn-primary" style={{ marginTop: '1rem' }}>
                        {loading ? 'Processing...' : 'Register Account'}
                    </button>

                    <p style={{ textAlign: 'center', marginTop: '1rem', color: 'var(--text-muted)' }}>
                        Already have an account? <Link to="/login" style={{ color: 'var(--color-primary)' }}>Login</Link>
                    </p>
                </form>
            </div>
        </div>
    );
}

export default Register;
