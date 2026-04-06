import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Login() {
    const [formData, setFormData] = useState({ email: '', password: '' });
    const [errorMsg, setErrorMsg] = useState('');
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();
    const { login } = useAuth();

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrorMsg('');
        setLoading(true);
        const res = await login(formData);
        setLoading(false);

        if (res.success) {
            navigate('/pharmacy-dashboard');
        } else {
            setErrorMsg(res.message);
        }
    };

    return (
        <div className="container" style={{ padding: '5rem 0', display: 'flex', justifyContent: 'center' }}>
            <div className="glass-panel" style={{ width: '100%', maxWidth: '400px' }}>
                <h2 style={{ textAlign: 'center', marginBottom: '2rem', color: 'var(--text-main)' }}>Welcome Back</h2>

                {errorMsg && (
                    <div style={{ padding: '0.8rem', marginBottom: '1rem', backgroundColor: 'rgba(239, 68, 68, 0.1)', color: 'var(--color-primary-dark)', borderRadius: '8px', border: '1px solid var(--color-primary-light)', fontSize: '0.9rem', textAlign: 'center' }}>
                        {errorMsg}
                    </div>
                )}

                <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
                    <div>
                        <label style={{ fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '0.3rem', display: 'block' }}>Email Address</label>
                        <input type="email" placeholder="example@pharmacy.com" required className="search-input" style={{ width: '100%', padding: '0.8rem', borderRadius: '8px' }}
                            onChange={(e) => setFormData({ ...formData, email: e.target.value })} disabled={loading} />
                    </div>
                    <div>
                        <label style={{ fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '0.3rem', display: 'block' }}>Password</label>
                        <input type="password" placeholder="Enter password" required className="search-input" style={{ width: '100%', padding: '0.8rem', borderRadius: '8px' }}
                            onChange={(e) => setFormData({ ...formData, password: e.target.value })} disabled={loading} />
                        <div style={{ textAlign: 'right', marginTop: '0.5rem' }}>
                            <a href="#" style={{ fontSize: '0.8rem', color: 'var(--text-muted)' }}>Forgot password?</a>
                        </div>
                    </div>

                    <button type="submit" disabled={loading} className="btn btn-primary" style={{ marginTop: '1rem' }}>
                        {loading ? 'Authenticating...' : 'Sign In'}
                    </button>

                    <p style={{ textAlign: 'center', marginTop: '1rem', color: 'var(--text-muted)', fontSize: '0.95rem' }}>
                        Don't have an account? <Link to="/register" style={{ color: 'var(--color-primary)' }}>Create one</Link>
                    </p>
                </form>
            </div>
        </div>
    );
}

export default Login;
