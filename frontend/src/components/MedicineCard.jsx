import { Link } from 'react-router-dom';
import { Package } from 'lucide-react';

function MedicineCard({ medicine }) {
    return (
        <Link to={`/medicines/${medicine.id}`} className="card animate-in" style={{ display: 'flex', flexDirection: 'column', height: '100%' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '1rem' }}>
                <div style={{ width: '48px', height: '48px', borderRadius: '12px', background: 'rgba(0,0,0,0.03)', display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'var(--color-primary)' }}>
                    <Package size={24} />
                </div>
                {medicine.pharmacies && medicine.pharmacies.length > 0 ? (
                    <span className="badge badge-red" style={{ fontSize: '0.7rem' }}>IN STOCK</span>
                ) : (
                    <span className="badge badge-outline" style={{ fontSize: '0.7rem', opacity: 0.5 }}>OUT OF STOCK</span>
                )}
            </div>
            <h3 style={{ textTransform: 'uppercase', marginBottom: '0.5rem' }}>{medicine.generic_name}</h3>
            <p style={{ marginBottom: '1.5rem', fontSize: '0.9rem', color: 'var(--text-muted)', flex: 1 }}>{medicine.brand_name || 'Generic Formulation'}</p>

            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', paddingTop: '1rem', borderTop: '1px solid rgba(0,0,0,0.05)' }}>
                <span style={{ fontSize: '0.8rem', fontWeight: 800 }}>
                    {medicine.pharmacies?.length || 0} Providers
                </span>
                <span style={{ color: 'var(--color-primary)', fontWeight: 800, fontSize: '0.85rem' }}>View Details →</span>
            </div>
        </Link>
    );
}

export default MedicineCard;
