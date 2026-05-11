import { Info, Target, Eye, ShieldCheck } from 'lucide-react';

export default function About() {
    return (
        <div className="container animate-in" style={{ padding: '6rem 0' }}>
            <div style={{ textAlign: 'center', marginBottom: '6rem' }}>
                <div style={{ display: 'inline-flex', alignItems: 'center', gap: '0.5rem', background: 'var(--color-primary-light)', color: 'var(--color-primary)', padding: '0.5rem 1.25rem', borderRadius: '100px', fontSize: '0.85rem', fontWeight: 800, marginBottom: '2rem', textTransform: 'uppercase', letterSpacing: '0.1em' }}>
                    <Info size={18} /> Our Story
                </div>
                <h1 style={{ fontSize: '4.5rem', marginBottom: '1.5rem', lineHeight: 1 }}>Mission Critical <span>Healthcare</span></h1>
                <p style={{ color: 'var(--text-muted)', fontSize: '1.25rem', maxWidth: '800px', margin: '0 auto' }}>MedLex Ethiopia is the nation's premier digital medication discovery network, built to bridge the gap between patients and verified clinical resources.</p>
            </div>

            <div className="grid" style={{ marginBottom: '8rem' }}>
                <div className="glass-panel" style={{ padding: '4rem' }}>
                    <div style={{ width: '64px', height: '64px', background: '#000', color: '#fff', borderRadius: '16px', display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: '2rem' }}>
                        <Target size={32} />
                    </div>
                    <h2 style={{ fontSize: '2.5rem', marginBottom: '1.5rem' }}>The Mission</h2>
                    <p style={{ color: 'var(--text-muted)', fontSize: '1.1rem', lineHeight: '1.8' }}>Our mission is to empower every Ethiopian citizen with instant access to verified medication data, reducing health risks and ensuring that life-saving pharmaceutical supplies are always locatable.</p>
                </div>
                <div className="glass-panel" style={{ padding: '4rem' }}>
                    <div style={{ width: '64px', height: '64px', background: 'var(--color-primary)', color: '#fff', borderRadius: '16px', display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: '2rem' }}>
                        <Eye size={32} />
                    </div>
                    <h2 style={{ fontSize: '2.5rem', marginBottom: '1.5rem' }}>The Vision</h2>
                    <p style={{ color: 'var(--text-muted)', fontSize: '1.1rem', lineHeight: '1.8' }}>We envision a future where Ethiopia's healthcare infrastructure is fully digitized, transparent, and responsive—where no patient is left searching for medicine in the dark.</p>
                </div>
            </div>

            <div className="glass-panel" style={{ padding: '6rem', background: '#000', color: '#fff', border: 'none', position: 'relative', overflow: 'hidden' }}>
                <div style={{ position: 'absolute', top: '-10%', right: '-5%', width: '400px', height: '400px', background: 'var(--color-primary)', opacity: 0.1, filter: 'blur(100px)', borderRadius: '50%' }}></div>
                <div style={{ position: 'relative', zIndex: 1 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', color: 'var(--color-primary)', marginBottom: '2rem' }}>
                        <ShieldCheck size={40} />
                        <h3 style={{ fontSize: '2rem', margin: 0, fontWeight: 900 }}>OFFICIALLY VERIFIED</h3>
                    </div>
                    <h2 style={{ fontSize: '3rem', marginBottom: '2rem', maxWidth: '700px' }}>Built in collaboration with local medical professionals.</h2>
                    <p style={{ color: 'rgba(255,255,255,0.6)', fontSize: '1.2rem', lineHeight: '1.8', maxWidth: '800px' }}>
                        MedLex is not just a search engine. It is a verified network of authorized pharmacies. Every provider in our system undergoes a strict licensing verification process to ensure the medication you find is genuine and safe.
                    </p>
                </div>
            </div>
        </div>
    );
}
