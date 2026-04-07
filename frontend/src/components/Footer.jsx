function Footer() {
    return (
        <footer style={{ borderTop: '1px solid var(--border-color)', padding: '2rem 0', marginTop: 'auto', backgroundColor: 'var(--bg-elem)' }}>
            <div className="container" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', color: 'var(--text-muted)', fontSize: '0.9rem' }}>
                <div>&copy; 2026 MedLex Ethiopia. All rights reserved.</div>
                <div style={{ display: 'flex', gap: '1rem' }}>
                    <a href="#" style={{ color: 'inherit', textDecoration: 'none' }}>Privacy</a>
                    <a href="#" style={{ color: 'inherit', textDecoration: 'none' }}>Terms</a>
                </div>
            </div>
        </footer>
    );
}

export default Footer;
