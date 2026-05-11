import { useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import { searchMedicines } from '../services/medicineService';
import MedicineCard from '../components/MedicineCard';
import { Search } from 'lucide-react';

function Medicines() {
    const [searchParams, setSearchParams] = useSearchParams();
    const query = searchParams.get('q') || '';

    const [medicines, setMedicines] = useState([]);
    const [search, setSearch] = useState(query);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        fetchMedicines(query);
    }, [query]);

    const fetchMedicines = async (term = '') => {
        setLoading(true);
        const result = await searchMedicines(term || 'a');
        if (result.success && result.data) {
            setMedicines(result.data);
        }
        setLoading(false);
    };

    const handleSearch = (e) => {
        e.preventDefault();
        setSearchParams({ q: search });
    };

    return (
        <div className="container animate-in" style={{ padding: '4rem 0' }}>
            <div style={{ textAlign: 'center', marginBottom: '4rem' }}>
                <span className="badge badge-red" style={{ marginBottom: '1rem' }}>Global Database</span>
                <h1 style={{ fontSize: '3.5rem', marginBottom: '1rem' }}>Medical <span>Inventory</span></h1>
                <p style={{ color: 'var(--text-muted)', maxWidth: '600px', margin: '0 auto' }}>Search through our comprehensive database of verified medicines and providers in Ethiopia.</p>
            </div>

            <form onSubmit={handleSearch} className="search-container" style={{ marginBottom: '4rem', boxShadow: 'var(--shadow-md)' }}>
                <input
                    type="text"
                    placeholder="Search by name, brand or generic..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                />
                <button type="submit"><Search size={18} /> Search</button>
            </form>

            {loading ? (
                <div style={{ textAlign: 'center', padding: '4rem' }}>Loading medicines...</div>
            ) : (
                <div className="grid">
                    {medicines.map((med) => (
                        <MedicineCard key={med.id} medicine={med} />
                    ))}
                </div>
            )}
        </div>
    );
}

export default Medicines;
