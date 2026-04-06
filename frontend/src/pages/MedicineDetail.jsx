import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { MapPin, Info, AlertTriangle, CheckCircle, Loader, ArrowLeft } from 'lucide-react';
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
                const res = await fetch(`http://localhost:8000/api/medicines.php?id=${id}`);
                const output = await res.json();
                if (output.status === 'success') {
                    setMedicine(output.data);
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

    if (loading) return <div style={{ display: 'flex', justifyContent: 'center', padding: '6rem' }}><Loader size={48} className="spinner" style={{ animation: 'spin 1s linear infinite', color: 'var(--color-primary)' }} /></div>;
    if (!medicine) return <div className="container" style={{ padding: '4rem 1.5rem', textAlign: 'center' }}><h2>Medicine not found</h2></div>;

    return (
        <div className="container" style={{ padding: '2rem 1.5rem' }}>
            <Link to="/medicines" className="animate-in" style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem', marginBottom: '2rem', color: 'var(--text-muted)', fontWeight: 500 }}>
                <ArrowLeft size={18} /> {t('btn_back')}
            </Link>

            <div className="glass-panel animate-in delay-1" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: '2rem', marginBottom: '3rem' }}>
                <div>
                    <span className="badge badge-red" style={{ marginBottom: '1.5rem' }}>{t('med_profile_badge')}</span>
                    <h1 style={{ fontSize: '3rem', margin: '0 0 0.5rem 0', letterSpacing: '-0.03em' }}>{medicine.generic_name}</h1>
                    <p style={{ color: 'var(--text-muted)', fontSize: '1.2rem' }}>{t('med_also_known')} <strong style={{ color: 'var(--text-main)' }}>{medicine.brand_names || 'N/A'}</strong></p>
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '2rem' }}>
                <div className="card animate-in delay-2">
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginBottom: '1.5rem', color: 'var(--color-primary-light)' }}>
                        <Info size={28} /> <h3 style={{ margin: 0, fontSize: '1.5rem' }}>{t('med_desc_usage')}</h3>
                    </div>
                    <p className="card-content" style={{ fontSize: '1.1rem', whiteSpace: 'pre-line', color: 'var(--text-main)', opacity: 0.9 }}>
                        {lang === 'am' && medicine.description_am ? medicine.description_am : medicine.description}
                        <br /><br />
                        <strong style={{ color: 'var(--text-muted)', fontSize: '0.9rem', textTransform: 'uppercase', letterSpacing: '0.05em' }}>{t('med_usage_rules')}</strong><br />
                        {lang === 'am' && medicine.usage_guidelines_am ? medicine.usage_guidelines_am : (medicine.usage_guidelines || 'Consult your doctor.')}
                    </p>
                </div>

                <div className="card animate-in delay-3">
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginBottom: '1.5rem', color: 'var(--color-success)' }}>
                        <CheckCircle size={28} /> <h3 style={{ margin: 0, fontSize: '1.5rem' }}>{t('med_dosage')}</h3>
                    </div>
                    <p className="card-content" style={{ fontSize: '1.1rem', whiteSpace: 'pre-line', color: 'var(--text-main)', opacity: 0.9 }}>
                        {lang === 'am' && medicine.dosage_am ? medicine.dosage_am : (medicine.dosage || 'Follow prescribed dosage.')}
                    </p>
                </div>

                <div className="card animate-in delay-4" style={{ gridColumn: '1 / -1', background: 'rgba(250, 204, 21, 0.03)', borderColor: 'rgba(250, 204, 21, 0.15)' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginBottom: '1.5rem', color: 'var(--color-secondary)' }}>
                        <AlertTriangle size={28} /> <h3 style={{ margin: 0, fontSize: '1.5rem' }}>{t('med_side_effects_warn')}</h3>
                    </div>
                    <div className="card-content" style={{ fontSize: '1.1rem', display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '2.5rem', color: 'var(--text-main)', opacity: 0.9 }}>
                        <div>
                            <strong style={{ color: 'var(--text-muted)', fontSize: '0.9rem', textTransform: 'uppercase', letterSpacing: '0.05em', display: 'block', marginBottom: '0.5rem' }}>{t('med_side_effects')}</strong>
                            {lang === 'am' && medicine.side_effects_am ? medicine.side_effects_am : (medicine.side_effects || 'None listed. Consult doctor.')}
                        </div>
                        <div>
                            <strong style={{ color: 'var(--text-muted)', fontSize: '0.9rem', textTransform: 'uppercase', letterSpacing: '0.05em', display: 'block', marginBottom: '0.5rem' }}>{t('med_safety_warn')}</strong>
                            {lang === 'am' && medicine.safety_warnings_am ? medicine.safety_warnings_am : (medicine.safety_warnings || 'Use as directed.')}
                        </div>
                    </div>
                </div>
            </div>

            <div className="animate-in delay-4" style={{ marginTop: '5rem', textAlign: 'center', background: 'rgba(239, 68, 68, 0.05)', padding: '4rem 2rem', borderRadius: '24px', border: '1px solid rgba(239, 68, 68, 0.1)', position: 'relative', overflow: 'hidden' }}>
                <div style={{ position: 'absolute', top: 0, left: '50%', transform: 'translate(-50%, -50%)', width: '300px', height: '300px', background: 'radial-gradient(circle, rgba(239, 68, 68, 0.2), transparent 70%)', zIndex: 0 }}></div>
                <div style={{ position: 'relative', zIndex: 1 }}>
                    <h2 style={{ fontSize: '2.25rem', marginBottom: '1.5rem' }}>{t('med_ready_find')}</h2>
                    <p style={{ color: 'var(--text-muted)', marginBottom: '2.5rem', fontSize: '1.1rem' }}>{t('med_discover_pharm')} <span style={{ color: 'white', fontWeight: 600 }}>{medicine.generic_name}</span>.</p>
                    <Link to={`/pharmacies?medicine_id=${medicine.id}`} className="btn btn-primary" style={{ padding: '1rem 2.5rem', fontSize: '1.1rem' }}>
                        <MapPin size={22} /> {t('med_btn_find_providers')}
                    </Link>
                </div>
            </div>
        </div>
    );
}
