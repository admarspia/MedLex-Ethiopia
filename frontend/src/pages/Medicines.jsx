import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { getMedicines } from '../services/medicineService';
import MedicineCard from '../components/MedicineCard';

function Medicines() {
    const [medicines, setMedicines] = useState([]);
    const [search, setSearch] = useState('');

    useEffect(() => {
        fetchMedicines();
    }, []);

    const fetchMedicines = async (term = '') => {
        const result = await getMedicines(term);
        if (result.data) {
            setMedicines(result.data);
        } else {
            setMedicines([
                { id: 1, generic_name: 'Paracetamol', brand_names: 'Panadol', details: 'Pain reliever' },
                { id: 2, generic_name: 'Amoxicillin', brand_names: 'Amoxil', details: 'Antibiotic' }
            ]);
        }
    };

    const handleSearch = (e) => {
        e.preventDefault();
        fetchMedicines(search);
    };

    return (
        <div className="container" style={{ padding: '3rem 0' }}>
            <h1 style={{ marginBottom: '2rem', textAlign: 'center', color: 'var(--text-main)' }}>Medicines</h1>
            <form onSubmit={handleSearch} className="search-container" style={{ marginBottom: '3rem' }}>
                <input 
                    type="text" 
                    placeholder="Search medicines..." 
                    className="search-input" 
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                />
            </form>
            <div className="grid">
                {medicines.map((med) => (
                    <MedicineCard key={med.id} medicine={med} />
                ))}
            </div>
        </div>
    );
}

export default Medicines;
