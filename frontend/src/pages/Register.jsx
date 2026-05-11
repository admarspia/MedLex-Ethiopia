import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { UserPlus, Upload, ShieldCheck } from 'lucide-react';

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
        <div className="container animate-in" style={{ padding: '6rem 0', display: 'flex', justifyContent: 'center' }}>
            <div className="glass-panel" style={{ width: '100%', maxWidth: '600px' }}>
                <div style={{ textAlign: 'center', marginBottom: '3rem' }}>
                    <div style={{ width: '64px', height: '64px', borderRadius: '12px', background: 'var(--color-primary)', color: '#fff', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', marginBottom: '1.5rem' }}>
                        <UserPlus size={32} />
                    </div>
                    <h2 style={{ fontSize: '2.4rem' }}>Pharmacy Network</h2>
                    <p style={{ color: 'var(--text-muted)' }}>Complete your verification to join the Ethiopian medication network</p>
                </div>

                {errorMsg && (
                    <div style={{ padding: '1rem', marginBottom: '2rem', backgroundColor: 'var(--color-primary-light)', color: 'var(--color-primary)', borderRadius: 'var(--border-radius-sm)', border: '1px solid rgba(239, 68, 68, 0.2)', fontSize: '0.9rem' }}>
                        {errorMsg}
                    </div>
                )}

                <form onSubmit={handleSubmit} style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem' }}>
                    <div style={{ gridColumn: '1 / -1' }}>
                        <label style={{ fontSize: '0.9rem', fontWeight: 600, marginBottom: '0.5rem', display: 'block' }}>Official Pharmacy Name</label>
                        <input type="text" placeholder="e.g. Life-Line Pharmacy Addis" required className="search-input"
                            onChange={(e) => setFormData({ ...formData, name: e.target.value })} disabled={loading} />
                    </div>
                    <div>
                        <label style={{ fontSize: '0.9rem', fontWeight: 600, marginBottom: '0.5rem', display: 'block' }}>Contact Email</label>
                        <input type="email" placeholder="admin@pharmacy.com" required className="search-input"
                            onChange={(e) => setFormData({ ...formData, email: e.target.value })} disabled={loading} />
                    </div>
                    <div>
                        <label style={{ fontSize: '0.9rem', fontWeight: 600, marginBottom: '0.5rem', display: 'block' }}>Official Phone</label>
                        <input type="text" placeholder="+251 911 222 333" required className="search-input"
                            onChange={(e) => setFormData({ ...formData, phone: e.target.value })} disabled={loading} />
                    </div>
                    <div style={{ gridColumn: '1 / -1' }}>
                        <label style={{ fontSize: '0.9rem', fontWeight: 600, marginBottom: '0.5rem', display: 'block' }}>Physical Address</label>
                        <input type="text" placeholder="Street, Woreda, City" required className="search-input"
                            onChange={(e) => setFormData({ ...formData, address: e.target.value })} disabled={loading} />
                    </div>
                    <div style={{ gridColumn: '1 / -1' }}>
                        <label style={{ fontSize: '0.9rem', fontWeight: 600, marginBottom: '0.5rem', display: 'block' }}>Account Password</label>
                        <input type="password" placeholder="Min. 6 characters" required minLength="6" className="search-input"
                            onChange={(e) => setFormData({ ...formData, password: e.target.value })} disabled={loading} />
                    </div>

                    <div style={{ gridColumn: '1 / -1', background: 'rgba(0,0,0,0.02)', padding: '2.5rem', borderRadius: 'var(--border-radius-md)', border: '2px dashed var(--border-color)', textAlign: 'center' }}>
                        <Upload size={32} style={{ color: 'var(--color-primary)', marginBottom: '1rem' }} />
                        <h4 style={{ marginBottom: '0.5rem' }}>Professional License</h4>
                        <p style={{ fontSize: '0.8rem', color: 'var(--text-muted)', marginBottom: '1.5rem' }}>Upload your pharmacy license (PDF/JPG) for verification</p>
                        <input type="file" accept=".pdf, .jpg, .jpeg, .png" required className="search-input" style={{ background: '#fff' }}
                            onChange={(e) => setLicenseFile(e.target.files[0])} disabled={loading} />
                    </div>

                    <div style={{ gridColumn: '1 / -1' }}>
                        <button type="submit" disabled={loading} className="btn btn-primary" style={{ width: '100%', height: '4rem', fontSize: '1.1rem' }}>
                            {loading ? 'Processing Application...' : 'Create Professional Account'}
                        </button>
                        <p style={{ marginTop: '1.5rem', fontSize: '0.8rem', color: 'var(--text-muted)', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '0.4rem' }}>
                            <ShieldCheck size={14} /> Data encrypted and stored following ET health regulations.
                        </p>
                    </div>

                    <p style={{ gridColumn: '1 / -1', textAlign: 'center', marginTop: '2rem', color: 'var(--text-muted)' }}>
                        Already part of the network? <Link to="/login" style={{ color: 'var(--color-primary)', fontWeight: 800 }}>Sign In</Link>
                    </p>
                </form>
            </div>
        </div>
    );
}

export default Register;
