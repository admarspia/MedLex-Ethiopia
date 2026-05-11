import { MapPin } from 'lucide-react';

function PharmacyCard({ pharmacy }) {
    return (
        <div className="card animate-in">
            <div style={{ color: 'var(--color-primary)', marginBottom: '1rem' }}>
                <MapPin size={28} />
            </div>
            <h3 style={{ fontSize: '1.25rem' }}>{pharmacy.name}</h3>
            <p style={{ fontSize: '0.9rem', marginBottom: '0.5rem' }}>{pharmacy.location || pharmacy.address}</p>
            <p style={{ color: 'var(--color-primary)', fontWeight: '700', fontSize: '0.85rem' }}>{pharmacy.contact || pharmacy.phone}</p>
        </div>
    );
}

export default PharmacyCard;
