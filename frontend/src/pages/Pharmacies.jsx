import { useState, useEffect } from 'react';
import { getPharmacies } from '../services/pharmacyService';
import PharmacyCard from '../components/PharmacyCard';

function Pharmacies() {
    const [pharmacies, setPharmacies] = useState([]);

    useEffect(() => {
        const fetchPharmacies = async () => {
            const result = await getPharmacies();
            if (result.data) {
                setPharmacies(result.data);
            } else {
                setPharmacies([
                    { id: 1, name: 'Lion Pharmacy', location: 'Addis Ababa', contact: '+251 911 123456' },
                    { id: 2, name: 'Biruk Pharmacy', location: 'Hawassa', contact: '+251 922 654321' }
                ]);
            }
        };
        fetchPharmacies();
    }, []);

    return (
        <div className="container" style={{ padding: '3rem 0' }}>
            <h1 style={{ marginBottom: '2rem', textAlign: 'center', color: 'var(--text-main)' }}>Pharmacies</h1>
            <div className="grid">
                {pharmacies.map((pharmacy) => (
                    <PharmacyCard key={pharmacy.id} pharmacy={pharmacy} />
                ))}
            </div>
        </div>
    );
}

export default Pharmacies;
