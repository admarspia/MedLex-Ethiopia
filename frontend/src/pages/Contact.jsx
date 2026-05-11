import { Mail, Phone, MapPin, Send, MessageSquare } from 'lucide-react';

export default function Contact() {
    return (
        <div className="container animate-in" style={{ padding: '6rem 0' }}>
            <div style={{ textAlign: 'center', marginBottom: '6rem' }}>
                <h1 style={{ fontSize: '4.5rem', marginBottom: '1.5rem' }}>Institutional <span>Support</span></h1>
                <p style={{ color: 'var(--text-muted)', fontSize: '1.25rem', maxWidth: '700px', margin: '0 auto' }}>Have questions about the network or need technical assistance? Our team is available 24/7.</p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1.5fr', gap: '4rem', alignItems: 'start' }}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '2rem' }}>
                    <div className="glass-panel" style={{ padding: '2.5rem' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '1.5rem' }}>
                            <div style={{ width: '48px', height: '48px', background: '#000', color: '#fff', borderRadius: '12px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                <Mail size={24} />
                            </div>
                            <div>
                                <h4 style={{ margin: 0, fontSize: '0.9rem', color: 'var(--text-muted)', textTransform: 'uppercase' }}>Email Us</h4>
                                <p style={{ margin: 0, fontWeight: 700, fontSize: '1.1rem' }}>support@medlex.et</p>
                            </div>
                        </div>
                    </div>
                    <div className="glass-panel" style={{ padding: '2.5rem' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '1.5rem' }}>
                            <div style={{ width: '48px', height: '48px', background: 'var(--color-primary)', color: '#fff', borderRadius: '12px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                <Phone size={24} />
                            </div>
                            <div>
                                <h4 style={{ margin: 0, fontSize: '0.9rem', color: 'var(--text-muted)', textTransform: 'uppercase' }}>Call Helpline</h4>
                                <p style={{ margin: 0, fontWeight: 700, fontSize: '1.1rem' }}>+251 911 222 333</p>
                            </div>
                        </div>
                    </div>
                    <div className="glass-panel" style={{ padding: '2.5rem' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '1.5rem' }}>
                            <div style={{ width: '48px', height: '48px', background: '#000', color: '#fff', borderRadius: '12px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                <MapPin size={24} />
                            </div>
                            <div>
                                <h4 style={{ margin: 0, fontSize: '0.9rem', color: 'var(--text-muted)', textTransform: 'uppercase' }}>Headquarters</h4>
                                <p style={{ margin: 0, fontWeight: 700, fontSize: '1.1rem' }}>Bole Road, Addis Ababa</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="glass-panel" style={{ padding: '4rem' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '3rem', color: 'var(--color-primary)' }}>
                        <MessageSquare size={32} />
                        <h2 style={{ fontSize: '2rem', margin: 0 }}>General Inquiry</h2>
                    </div>
                    <form style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '2rem' }}>
                        <div>
                            <label style={{ display: 'block', marginBottom: '0.75rem', fontWeight: 600 }}>Your Name</label>
                            <input type="text" className="search-input" placeholder="Abebe Bikila" />
                        </div>
                        <div>
                            <label style={{ display: 'block', marginBottom: '0.75rem', fontWeight: 600 }}>Email Address</label>
                            <input type="email" className="search-input" placeholder="abebe@mail.com" />
                        </div>
                        <div style={{ gridColumn: '1 / -1' }}>
                            <label style={{ display: 'block', marginBottom: '0.75rem', fontWeight: 600 }}>Subject</label>
                            <input type="text" className="search-input" placeholder="How can we help?" />
                        </div>
                        <div style={{ gridColumn: '1 / -1' }}>
                            <label style={{ display: 'block', marginBottom: '0.75rem', fontWeight: 600 }}>Detailed Message</label>
                            <textarea className="search-input" style={{ height: '150px', paddingTop: '1rem' }} placeholder="Provide as much detail as possible..."></textarea>
                        </div>
                        <div style={{ gridColumn: '1 / -1' }}>
                            <button type="submit" className="btn btn-primary" style={{ width: '100%', height: '4rem', fontSize: '1.1rem' }}>
                                <Send size={20} /> Send Professional Inquiry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
