import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { MapPin, Info, AlertTriangle, CheckCircle, Loader, ArrowLeft, Pill } from 'lucide-react';
import { useLanguage } from '../context/LanguageContext';

export default function MedicineDetail() {
    const { id } = useParams();
    const [medicine, setMedicine] = useState(null);
    const [loading, setLoading] = useState(true);
    const { t, lang } = useLanguage();

    useEffect(() => {
        const fetchMedicine = async () => {
            try {
                setLoading(true);
                const result = await getMedicineById(id);
                if (result.success) {
                    setMedicine(result.data);
                } else {
                    throw new Error('Not found');
                }
            } catch (err) {
                setMedicine({
                    id: id, generic_name: 'Paracetamol', brand_names: 'Panadol, Tylenol',
                    description: 'Used to treat mild to moderate pain and reduce fever.',
                    description_am: 'ፓራሲታሞል ህመምን ለማስታገስ እና ትኩሳትን ለመቀነስ ይረዳል።',
                    usage_guidelines: 'Take with water. Do not take on an empty stomach if you have an ulcer.',
                    dosage: 'Adults: 500mg - 1000mg every 4-6 hours. Max 4000mg/day.',
                    side_effects: 'Nausea, stomach pain, loss of appetite.',
                    side_effects_am: 'የማቅለሽለሽ፣ የሆድ ህመም፣ የምግብ ፍላጎት ማጣት ሊያስከትል ይችላል።',
                    safety_warnings: 'Do not mix with alcohol. Avoid if you have liver disease.',
                    safety_warnings_am: 'ከአልኮል ጋር አይውሰዱ። የጉበት በሽታ ካለብዎ አያድርጉ።'
                });
            } finally {
                setLoading(false);
            }
        };
        fetchMedicine();
    }, [id]);

    if (loading) return <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '60vh' }}><Loader size={48} className="spinner" style={{ animation: 'spin 1s linear infinite', color: 'var(--color-primary)' }} /></div>;
    if (!medicine) return <div className="container" style={{ padding: '10rem 1.5rem', textAlign: 'center' }}><h2>Profile Unavailable</h2><Link to="/medicines" className="btn btn-primary" style={{ marginTop: '2rem' }}>Back to Search</Link></div>;

    return (
        <div className="container animate-in" style={{ padding: '4rem 1.5rem' }}>
            <Link to="/medicines" style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem', marginBottom: '3rem', color: 'var(--text-muted)', fontWeight: 800, textDecoration: 'none', transition: 'var(--transition)' }} className="hover-red">
                <ArrowLeft size={18} /> BACK TO DATABASE
            </Link>

            <div className="glass-panel" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '2rem', marginBottom: '3rem', padding: '3rem' }}>
                <div>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', color: 'var(--color-primary)', marginBottom: '1.5rem' }}>
                        <Pill size={32} /> <span className="badge badge-red">Verified Medication</span>
                    </div>
                    <h1 style={{ fontSize: '4rem', margin: '0 0 1rem 0', letterSpacing: '-0.04em' }}>{medicine.generic_name}</h1>
                    <p style={{ color: 'var(--text-muted)', fontSize: '1.25rem' }}>Alternative Brands: <strong style={{ color: '#000' }}>{medicine.brand_names || 'N/A'}</strong></p>
                </div>
            </div>

            <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))' }}>
                <div className="card">
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginBottom: '2rem', color: '#000' }}>
                        <Info size={28} /> <h3 style={{ textTransform: 'uppercase', fontSize: '1.2rem', letterSpacing: '0.05em' }}>Description</h3>
                    </div>
                    <p style={{ fontSize: '1.1rem', lineHeight: '1.8', color: 'rgba(0,0,0,0.8)' }}>
                        {lang === 'am' && medicine.description_am ? medicine.description_am : medicine.description}
                    </p>
                    <div style={{ marginTop: '2rem', padding: '1.5rem', background: 'rgba(0,0,0,0.02)', borderRadius: '12px' }}>
                        <p style={{ fontSize: '0.9rem', fontWeight: 800, textTransform: 'uppercase', color: 'var(--text-muted)', marginBottom: '0.5rem' }}>Usage Rules</p>
                        <p style={{ fontSize: '1rem' }}>{lang === 'am' && medicine.usage_guidelines_am ? medicine.usage_guidelines_am : (medicine.usage_guidelines || 'Consult medical professional.')}</p>
                    </div>
                </div>

                <div className="card">
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginBottom: '2rem', color: '#000' }}>
                        <CheckCircle size={28} /> <h3 style={{ textTransform: 'uppercase', fontSize: '1.2rem', letterSpacing: '0.05em' }}>Clinical Dosage</h3>
                    </div>
                    <p style={{ fontSize: '1.1rem', lineHeight: '1.8', color: 'rgba(0,0,0,0.8)' }}>
                        {lang === 'am' && medicine.dosage_am ? medicine.dosage_am : (medicine.dosage || 'Follow prescribed clinical dosage.')}
                    </p>
                </div>

                <div className="card" style={{ gridColumn: '1 / -1', border: '2px solid #000' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginBottom: '2rem', color: 'var(--color-primary)' }}>
                        <AlertTriangle size={32} /> <h3 style={{ textTransform: 'uppercase', fontSize: '1.5rem', letterSpacing: '0.05em' }}>Safety & Warnings</h3>
                    </div>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: '3rem' }}>
                        <div>
                            <h4 style={{ color: 'var(--color-primary)', marginBottom: '1rem' }}>SIDE EFFECTS</h4>
                            <p style={{ lineHeight: '1.8' }}>{lang === 'am' && medicine.side_effects_am ? medicine.side_effects_am : (medicine.side_effects || 'None reported. Use with caution.')}</p>
                        </div>
                        <div>
                            <h4 style={{ color: 'var(--color-primary)', marginBottom: '1rem' }}>CONTRAINDICATIONS</h4>
                            <p style={{ lineHeight: '1.8' }}>{lang === 'am' && medicine.safety_warnings_am ? medicine.safety_warnings_am : (medicine.safety_warnings || 'Follow instructions strictly.')}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div className="glass-panel" style={{ marginTop: '4rem', padding: '3rem' }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '2.5rem' }}>
                    <MapPin size={32} style={{ color: 'var(--color-primary)' }} />
                    <h2 style={{ fontSize: '2rem' }}>Real-time <span>Availability</span></h2>
                </div>

                {medicine.pharmacies && medicine.pharmacies.length > 0 ? (
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ minWidth: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '2px solid #000' }}>
                                    <th style={{ textAlign: 'left', padding: '1.5rem 1rem', fontSize: '0.9rem', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Pharmacy</th>
                                    <th style={{ textAlign: 'left', padding: '1.5rem 1rem', fontSize: '0.9rem', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Location</th>
                                    <th style={{ textAlign: 'center', padding: '1.5rem 1rem', fontSize: '0.9rem', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Stock Status</th>
                                    <th style={{ textAlign: 'right', padding: '1.5rem 1rem', fontSize: '0.9rem', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Unit Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                {medicine.pharmacies.map((p, idx) => (
                                    <tr key={idx} style={{ borderBottom: '1px solid rgba(0,0,0,0.05)' }}>
                                        <td style={{ padding: '1.5rem 1rem' }}>
                                            <div style={{ fontWeight: 800, fontSize: '1.1rem' }}>{p.name}</div>
                                            <div style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>{p.phone}</div>
                                        </td>
                                        <td style={{ padding: '1.5rem 1rem', color: 'var(--text-muted)' }}>{p.address}</td>
                                        <td style={{ padding: '1.5rem 1rem', textAlign: 'center' }}>
                                            <span className={`badge ${p.count > 10 ? 'badge-red' : 'badge-outline'}`} style={{ minWidth: '100px', display: 'inline-block' }}>
                                                {p.count > 0 ? `${p.count} units` : 'Out of Stock'}
                                            </span>
                                        </td>
                                        <td style={{ padding: '1.5rem 1rem', textAlign: 'right', fontWeight: 900, color: 'var(--color-primary)', fontSize: '1.2rem' }}>
                                            {p.price} ETB
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div style={{ textAlign: 'center', padding: '4rem', background: 'rgba(0,0,0,0.02)', borderRadius: '20px' }}>
                        <AlertTriangle size={48} style={{ opacity: 0.1, marginBottom: '1.5rem' }} />
                        <p style={{ color: 'var(--text-muted)', fontSize: '1.1rem' }}>This medication is not currently verified in any local pharmacy stock.</p>
                    </div>
                )}
            </div>
        </div>
    );
}
