import { useState, useEffect } from 'react';
import { getPharmacies } from '../services/pharmacyService';
import PharmacyCard from '../components/PharmacyCard';
import { MapPin } from 'lucide-react';

function Pharmacies() {
    const [pharmacies, setPharmacies] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchPharmacies = async () => {
            setLoading(true);
            const result = await getPharmacies();
            console.log(result);
            if (result.status == 200 && result.data) {
                setPharmacies(result.data);
            }
            
            setLoading(false);
        };
        fetchPharmacies();
    }, []);

    return (
        <div className="container animate-in" style={{ padding: '4rem 0' }}>
            <div style={{ textAlign: 'center', marginBottom: '4rem' }}>
                <div style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem', background: 'var(--color-primary-light)', color: 'var(--color-primary)', padding: '0.5rem 1rem', borderRadius: '100px', fontSize: '0.8rem', fontWeight: 800, marginBottom: '1.5rem', textTransform: 'uppercase' }}>
                    <MapPin size={16} /> Nationwide Network
                </div>
                <h1 style={{ fontSize: '3.5rem', marginBottom: '1rem' }}>Local <span>Providers</span></h1>
                <p style={{ color: 'var(--text-muted)', maxWidth: '600px', margin: '0 auto' }}>Locate verified pharmacies across Ethiopia and check their medication availability in real-time.</p>
            </div>

            {loading ? (
                <div style={{ textAlign: 'center', padding: '4rem' }}>Searching for providers...</div>
            ) : (
                <div className="grid">
                    {pharmacies.map((pharmacy) => (
                        <PharmacyCard key={pharmacy.id} pharmacy={pharmacy} />
                    ))}
                </div>
            )}
        </div>
    );
}

export default Pharmacies;
