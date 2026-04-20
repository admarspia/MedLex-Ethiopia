import { Search, ShieldAlert, Activity, BookOpen, Clock, Heart } from 'lucide-react';

export default function Services() {
    const services = [
        {
            icon: <Search size={32} />,
            title: "Advanced Search",
            desc: "Locate any medication in Ethiopia using generic or brand names with real-time stock availability.",
            color: "#000"
        },
        {
            icon: <ShieldAlert size={32} />,
            title: "Safety Verification",
            desc: "Instant access to FDA-verified medication labels, safety warnings, and clinical dosage guidelines.",
            color: "var(--color-primary)"
        },
        {
            icon: <Activity size={32} />,
            title: "Live Inventory",
            desc: "Pharmacies update their stock levels in real-time, ensuring you never travel to a provider out of stock.",
            color: "#000"
        },
        {
            icon: <BookOpen size={32} />,
            title: "Educational Hub",
            desc: "Detailed guides in Amharic and English about common medication usage and potential side effects.",
            color: "var(--color-primary)"
        },
        {
            icon: <Clock size={32} />,
            title: "Emergency Network",
            desc: "Priority access to critical care medications and 24/7 provider contact information.",
            color: "#000"
        },
        {
            icon: <Heart size={32} />,
            title: "Patient Support",
            desc: "Resources for chronic illness management and affordable medication discovery.",
            color: "var(--color-primary)"
        }
    ];

    return (
        <div className="container animate-in" style={{ padding: '6rem 0' }}>
            <div style={{ textAlign: 'center', marginBottom: '8rem' }}>
                <h1 style={{ fontSize: '4.5rem', marginBottom: '1.5rem' }}>Premium Health <span>Services</span></h1>
                <p style={{ color: 'var(--text-muted)', fontSize: '1.25rem', maxWidth: '700px', margin: '0 auto' }}>Explore the suite of tools and networks designed to modernize the Ethiopian pharmaceutical landscape.</p>
            </div>

            <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))', gap: '3rem' }}>
                {services.map((s, i) => (
                    <div key={i} className="glass-panel" style={{ padding: '3rem', height: '100%' }}>
                        <div style={{ width: '64px', height: '64px', background: s.color, color: '#fff', borderRadius: '16px', display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: '2rem' }}>
                            {s.icon}
                        </div>
                        <h3 style={{ fontSize: '1.8rem', marginBottom: '1rem' }}>{s.title}</h3>
                        <p style={{ color: 'var(--text-muted)', lineHeight: '1.8' }}>{s.desc}</p>
                    </div>
                ))}
            </div>

            <div style={{ marginTop: '8rem', background: 'rgba(239, 68, 68, 0.05)', padding: '6rem', borderRadius: '40px', border: '1px solid rgba(239, 68, 68, 0.1)', textAlign: 'center' }}>
                <h2 style={{ fontSize: '3rem', marginBottom: '2rem' }}>Ready to join the network?</h2>
                <p style={{ color: 'var(--text-muted)', fontSize: '1.2rem', marginBottom: '3rem', maxWidth: '600px', margin: '0 auto 3rem' }}>Whether you are a patient looking for help or a pharmacy ready to digitize your inventory, we have the tools you need.</p>
                <div style={{ display: 'flex', gap: '1.5rem', justifyContent: 'center' }}>
                    <button className="btn btn-primary" style={{ padding: '1rem 2.5rem' }}>Start Searching</button>
                    <button className="btn btn-outline" style={{ padding: '1rem 2.5rem' }}>Register Pharmacy</button>
                </div>
            </div>
        </div>
    );
}
