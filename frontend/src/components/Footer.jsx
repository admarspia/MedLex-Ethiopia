function Footer() {
    return (
        <footer style={{ borderTop: '2px solid #000', padding: '4rem 0', marginTop: 'auto', backgroundColor: '#000', color: '#fff' }}>
            <div className="container" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '2rem' }}>
                <div>
                    <h3 style={{ color: 'var(--color-primary)', fontSize: '1.5rem', marginBottom: '1rem', fontWeight: 900 }}>MEDLEX <span style={{ color: '#fff' }}>ETHIOPIA</span></h3>
                    <p style={{ opacity: 0.6, fontSize: '0.85rem', maxWidth: '300px' }}>The official medication discovery and pharmacy network for Ethiopia. Verified information at your fingertips.</p>
                </div>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem', textAlign: 'right' }}>
                    <div style={{ display: 'flex', gap: '2rem', fontWeight: 700, fontSize: '0.9rem' }}>
                        <a href="#" style={{ color: 'inherit', textDecoration: 'none' }} className="hover-red">PRIVACY</a>
                        <a href="#" style={{ color: 'inherit', textDecoration: 'none' }} className="hover-red">TERMS</a>
                        <a href="#" style={{ color: 'inherit', textDecoration: 'none' }} className="hover-red">CONTACT</a>
                    </div>
                    <div style={{ opacity: 0.4, fontSize: '0.75rem' }}>&copy; 2026 MedLex Ethiopia. High-Impact Healthcare.</div>
                </div>
            </div>
        </footer>
    );
}

export default Footer;
