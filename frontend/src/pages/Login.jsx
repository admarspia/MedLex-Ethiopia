import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { Lock, Mail, ArrowRight } from 'lucide-react';

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
        <div className="container animate-in" style={{ padding: '6rem 0', display: 'flex', justifyContent: 'center' }}>
            <div className="glass-panel" style={{ width: '100%', maxWidth: '450px' }}>
                <div style={{ textAlign: 'center', marginBottom: '2.5rem' }}>
                    <div style={{ width: '64px', height: '64px', borderRadius: '50%', background: 'var(--color-primary-light)', color: 'var(--color-primary)', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', marginBottom: '1.5rem' }}>
                        <Lock size={32} />
                    </div>
                    <h2 style={{ fontSize: '2rem' }}>Welcome Back</h2>
                    <p style={{ color: 'var(--text-muted)' }}>Sign in to manage your pharmacy</p>
                </div>

                {errorMsg && (
                    <div style={{ padding: '1rem', marginBottom: '2rem', backgroundColor: 'var(--color-primary-light)', color: 'var(--color-primary)', borderRadius: 'var(--border-radius-sm)', border: '1px solid rgba(239, 68, 68, 0.2)', fontSize: '0.9rem', textAlign: 'center' }}>
                        {errorMsg}
                    </div>
                )}

                <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '1.5rem' }}>
                    <div>
                        <label style={{ fontSize: '0.9rem', fontWeight: 600, marginBottom: '0.5rem', display: 'block' }}>Email Address</label>
                        <div style={{ position: 'relative' }}>
                            <Mail size={18} style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)', color: 'var(--text-muted)' }} />
                            <input type="email" placeholder="pharmacy@example.com" required className="search-input" style={{ paddingLeft: '3rem' }}
                                onChange={(e) => setFormData({ ...formData, email: e.target.value })} disabled={loading} />
                        </div>
                    </div>
                    <div>
                        <label style={{ fontSize: '0.9rem', fontWeight: 600, marginBottom: '0.5rem', display: 'block' }}>Password</label>
                        <input type="password" placeholder="••••••••" required className="search-input"
                            onChange={(e) => setFormData({ ...formData, password: e.target.value })} disabled={loading} />
                    </div>

                    <button type="submit" disabled={loading} className="btn btn-primary" style={{ height: '3.5rem', fontSize: '1rem' }}>
                        {loading ? 'Verifying...' : 'Sign In Now'} <ArrowRight size={18} />
                    </button>

                    <p style={{ textAlign: 'center', color: 'var(--text-muted)', fontSize: '0.95rem' }}>
                        New to MedLex? <Link to="/register" style={{ color: 'var(--color-primary)', fontWeight: 700 }}>Apply for an Account</Link>
                    </p>
                </form>
            </div>
        </div>
    );
}

export default Login;
