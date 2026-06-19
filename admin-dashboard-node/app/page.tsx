'use client';

import { useEffect, useMemo, useState } from 'react';
import { useRouter } from 'next/navigation';
import { createBrowserClient } from '@/lib/supabase-browser';
import type { Session } from '@supabase/supabase-js';

const defaultStats = {
  gallery: 0,
  events: 0,
  contacts: 0,
  newContacts: 0,
};

const defaultAbout = {
  hero_title: '',
  hero_subtitle: '',
  hero_description: '',
  mission: '',
  vision: '',
};

const defaultSettings = {
  site_name: '',
  site_email: '',
  site_phone: '',
  site_address: '',
  site_description: '',
  social_links: {
    facebook: '',
    twitter: '',
    linkedin: '',
    instagram: '',
  },
};

const tabs = ['dashboard', 'community', 'contacts', 'about', 'business-talk', 'settings'] as const;

type Tab = (typeof tabs)[number];

type BusinessTalkRegistration = {
  id?: string;
  name: string;
  email: string;
  phone?: string;
  company?: string;
  [key: string]: any;
};

type GalleryItem = { id: number; image_url: string; caption: string; created_at?: string };
type EventItem = { id: number; title: string; date: string; description: string; image_url: string; created_at?: string };
type ContactSubmission = {
  id: number;
  name: string;
  email: string;
  company: string | null;
  service: string | null;
  message: string;
  status: 'new' | 'replied' | 'archived';
  created_at: string;
};
type AboutContent = typeof defaultAbout;
type SettingsContent = typeof defaultSettings;

type Stats = typeof defaultStats;

function SectionHeader({ title, subtitle }: { title: string; subtitle: string }) {
  return (
    <div className="panel-header">
      <div>
        <h2>{title}</h2>
        <p className="subtle">{subtitle}</p>
      </div>
    </div>
  );
}

export default function Home() {
  const [activeTab, setActiveTab] = useState<Tab>('dashboard');
  const [loading, setLoading] = useState(true);
  const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);
  const [stats, setStats] = useState<Stats>(defaultStats);
  const [gallery, setGallery] = useState<GalleryItem[]>([]);
  const [events, setEvents] = useState<EventItem[]>([]);
  const [contacts, setContacts] = useState<ContactSubmission[]>([]);
  const [about, setAbout] = useState<AboutContent>(defaultAbout);
  const [settings, setSettings] = useState<SettingsContent>(defaultSettings);
  const [businessTalkRegistrations, setBusinessTalkRegistrations] = useState<BusinessTalkRegistration[]>([]);
  const [businessTalkLoading, setBusinessTalkLoading] = useState(false);
  const [session, setSession] = useState<Session | null>(null);
  const [authLoading, setAuthLoading] = useState(true);
  const [supabaseClient] = useState(() => createBrowserClient());
  const router = useRouter();

  const [galleryForm, setGalleryForm] = useState({ caption: '', image_url: '' });
  const [eventForm, setEventForm] = useState({ title: '', date: '', description: '', image_url: '' });
  const [contactForm, setContactForm] = useState({ name: '', email: '', company: '', service: '', message: '' });

  const refreshAll = async () => {
    setLoading(true);
    try {
      const [statsRes, galleryRes, eventsRes, contactsRes, aboutRes, settingsRes] = await Promise.all([
        fetch('/api/stats'),
        fetch('/api/community?scope=gallery'),
        fetch('/api/community?scope=events'),
        fetch('/api/contact-submissions'),
        fetch('/api/about'),
        fetch('/api/settings'),
      ]);

      const [statsData, galleryData, eventsData, contactsData, aboutData, settingsData] = await Promise.all([
        statsRes.json(),
        galleryRes.json(),
        eventsRes.json(),
        contactsRes.json(),
        aboutRes.json(),
        settingsRes.json(),
      ]);

      setStats(statsData);
      setGallery(galleryData.items ?? []);
      setEvents(eventsData.items ?? []);
      setContacts(contactsData.items ?? []);
      setAbout(aboutData.item ?? defaultAbout);
      setSettings(settingsData.item ?? defaultSettings);
    } catch (error) {
      setMessage({ type: 'error', text: 'Unable to load dashboard data right now.' });
    } finally {
      setLoading(false);
    }
  };

  const fetchBusinessTalkRegistrations = async () => {
  setBusinessTalkLoading(true);
  try {
    const response = await fetch('/api/business-talk'); // ← changed, no headers needed
    
    if (!response.ok) {
      showMessage('error', 'Unable to fetch business talk registrations.');
      return;
    }

    const data = await response.json();
    setBusinessTalkRegistrations(data.registrations ?? []);
  } catch (error) {
    showMessage('error', 'Failed to fetch business talk data.');
  } finally {
    setBusinessTalkLoading(false);
  }
};

  useEffect(() => {
    if (activeTab === 'business-talk') {
      void fetchBusinessTalkRegistrations();
    }
  }, [activeTab]);

  useEffect(() => {
    const loadSession = async () => {
      const { data } = await supabaseClient.auth.getSession();
      setSession(data.session);
      setAuthLoading(false);
    };

    loadSession();
    const { data: listener } = supabaseClient.auth.onAuthStateChange((_event, authSession) => {
      setSession(authSession);
    });

    return () => listener.subscription.unsubscribe();
  }, [supabaseClient]);

  useEffect(() => {
    if (!authLoading) {
      if (!session) {
        router.replace('/login');
      } else {
        void refreshAll();
      }
    }
  }, [authLoading, session, router]);

  const contactCountLabel = useMemo(() => `${contacts.length} total`, [contacts.length]);

  const showMessage = (type: 'success' | 'error', text: string) => {
    setMessage({ type, text });
    window.setTimeout(() => setMessage(null), 3200);
  };

  const handleCreateGallery = async () => {
    const response = await fetch('/api/community?scope=gallery', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ image_url: galleryForm.image_url, caption: galleryForm.caption }),
    });

    const payload = await response.json();
    if (!response.ok) {
      showMessage('error', payload.error || 'Unable to save gallery image.');
      return;
    }

    setGalleryForm({ caption: '', image_url: '' });
    showMessage('success', 'Gallery image saved successfully.');
    void refreshAll();
  };

  const handleCreateEvent = async () => {
    const response = await fetch('/api/community?scope=events', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        title: eventForm.title,
        date: eventForm.date,
        description: eventForm.description,
        image_url: eventForm.image_url,
      }),
    });

    const payload = await response.json();
    if (!response.ok) {
      showMessage('error', payload.error || 'Unable to create event.');
      return;
    }

    setEventForm({ title: '', date: '', description: '', image_url: '' });
    showMessage('success', 'Event saved successfully.');
    void refreshAll();
  };

  const handleDeleteCommunity = async (scope: 'gallery' | 'events', id: number) => {
    const response = await fetch(`/api/community?scope=${scope}&id=${id}`, { method: 'DELETE' });
    const payload = await response.json();
    if (!response.ok) {
      showMessage('error', payload.error || 'Unable to delete item.');
      return;
    }

    showMessage('success', 'Item deleted successfully.');
    void refreshAll();
  };

  const handleAddContact = async () => {
    const response = await fetch('/api/contact-submissions', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(contactForm),
    });

    const payload = await response.json();
    if (!response.ok) {
      showMessage('error', payload.error || 'Unable to create contact submission.');
      return;
    }

    setContactForm({ name: '', email: '', company: '', service: '', message: '' });
    showMessage('success', 'Contact saved successfully.');
    void refreshAll();
  };

  const handleStatusChange = async (id: number, status: ContactSubmission['status']) => {
    const response = await fetch('/api/contact-submissions', {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, status }),
    });

    const payload = await response.json();
    if (!response.ok) {
      showMessage('error', payload.error || 'Unable to update status.');
      return;
    }

    showMessage('success', 'Status updated.');
    void refreshAll();
  };

  const handleDeleteContact = async (id: number) => {
    const response = await fetch(`/api/contact-submissions?id=${id}`, { method: 'DELETE' });
    const payload = await response.json();
    if (!response.ok) {
      showMessage('error', payload.error || 'Unable to delete contact.');
      return;
    }

    showMessage('success', 'Contact removed.');
    void refreshAll();
  };

  const handleSaveAbout = async () => {
    const response = await fetch('/api/about', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(about),
    });

    const payload = await response.json();
    if (!response.ok) {
      showMessage('error', payload.error || 'Unable to save about content.');
      return;
    }

    showMessage('success', 'About content saved.');
  };

  const handleSaveSettings = async () => {
    const response = await fetch('/api/settings', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(settings),
    });

    const payload = await response.json();
    if (!response.ok) {
      showMessage('error', payload.error || 'Unable to save settings.');
      return;
    }

    showMessage('success', 'Settings updated.');
  };

  const handleSignOut = async () => {
    const { error } = await supabaseClient.auth.signOut();
    if (error) {
      showMessage('error', error.message || 'Unable to sign out.');
      return;
    }
    setSession(null);
    router.replace('/login');
  };

  if (authLoading) {
    return (
      <main className="app-shell">
        <p className="subtle">Checking admin session…</p>
      </main>
    );
  }

  if (!session) {
    return (
      <main className="app-shell">
        <p className="subtle">Redirecting to admin login…</p>
      </main>
    );
  }

  return (
    <main className="app-shell">
      <div className="topbar">
        <div className="brand">
          <div className="brand-mark">C</div>
          <div>
            <h1>ComeCode Admin Dashboard</h1>
            <p>Supabase-backed operations for Vercel deployment.</p>
          </div>
        </div>

        <div className="nav-tabs">
          {tabs.map((tab) => (
            <button
              key={tab}
              className={`tab-button ${activeTab === tab ? 'active' : ''}`}
              onClick={() => setActiveTab(tab)}
            >
              {tab.charAt(0).toUpperCase() + tab.slice(1)}
            </button>
          ))}
        </div>

        <div>
          <button className="secondary-btn" onClick={handleSignOut}>
            Sign out
          </button>
        </div>
      </div>

      {message ? <div className={`alert ${message.type}`}>{message.text}</div> : null}

      {activeTab === 'dashboard' ? (
        <section>
          <div className="dashboard-grid">
            <div className="stat-card">
              <p className="stat-title">Gallery</p>
              <p className="stat-value">{stats.gallery}</p>
              <p className="stat-trend">Curated visuals for the community page.</p>
            </div>
            <div className="stat-card">
              <p className="stat-title">Events</p>
              <p className="stat-value">{stats.events}</p>
              <p className="stat-trend">Latest upcoming activations and campaign moments.</p>
            </div>
            <div className="stat-card">
              <p className="stat-title">Contacts</p>
              <p className="stat-value">{stats.contacts}</p>
              <p className="stat-trend">{contactCountLabel} saved in Supabase.</p>
            </div>
            <div className="stat-card">
              <p className="stat-title">New leads</p>
              <p className="stat-value">{stats.newContacts}</p>
              <p className="stat-trend">Ready for immediate follow-up.</p>
            </div>
          </div>

          <div className="content-grid">
            <div className="panel">
              <SectionHeader title="Recent Gallery" subtitle="Latest visuals are instantly available in the community section." />
              {gallery.length === 0 ? (
                <div className="empty-state">No gallery items yet. Add a new image in the Community tab.</div>
              ) : (
                <div className="preview-grid">
                  {gallery.slice(0, 3).map((item) => (
                    <div key={item.id} className="item-card">
                      <div className="item-title">{item.caption || 'Untitled gallery item'}</div>
                      <div className="item-copy">{item.image_url}</div>
                    </div>
                  ))}
                </div>
              )}
            </div>

            <div className="panel">
              <SectionHeader title="Latest contacts" subtitle="A quick snapshot of inbound interest and status." />
              {contacts.length === 0 ? (
                <div className="empty-state">No inquiries yet. Add a lead manually or wait for form submissions.</div>
              ) : (
                <div className="preview-grid">
                  {contacts.slice(0, 3).map((item) => (
                    <div key={item.id} className="item-card">
                      <div className="item-title">{item.name}</div>
                      <div className="item-copy">{item.email}</div>
                      <div className="chip-row">
                        <span className="chip">{item.status}</span>
                        {item.service ? <span className="chip">{item.service}</span> : null}
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        </section>
      ) : null}

      {activeTab === 'community' ? (
        <section className="content-grid">
          <div className="panel">
            <SectionHeader title="Add gallery image" subtitle="Feed the site with marketing-ready visuals and captions." />
            <div className="form-grid">
              <div className="field">
                <label>Image URL</label>
                <input
                  value={galleryForm.image_url}
                  onChange={(event) => setGalleryForm((current) => ({ ...current, image_url: event.target.value }))}
                  placeholder="https://example.com/gallery-image.jpg"
                />
              </div>
              <div className="field">
                <label>Caption</label>
                <input
                  value={galleryForm.caption}
                  onChange={(event) => setGalleryForm((current) => ({ ...current, caption: event.target.value }))}
                  placeholder="Short caption for the gallery"
                />
              </div>
              <button className="primary-btn" onClick={handleCreateGallery}>Save gallery image</button>
            </div>
          </div>

          <div className="panel">
            <SectionHeader title="Add event" subtitle="Publish events with a linkable image and rich details." />
            <div className="form-grid">
              <div className="field">
                <label>Title</label>
                <input
                  value={eventForm.title}
                  onChange={(event) => setEventForm((current) => ({ ...current, title: event.target.value }))}
                  placeholder="Design Sprint Session"
                />
              </div>
              <div className="field">
                <label>Date</label>
                <input
                  type="date"
                  value={eventForm.date}
                  onChange={(event) => setEventForm((current) => ({ ...current, date: event.target.value }))}
                />
              </div>
              <div className="field">
                <label>Description</label>
                <textarea
                  value={eventForm.description}
                  onChange={(event) => setEventForm((current) => ({ ...current, description: event.target.value }))}
                  placeholder="Write a clear event description"
                />
              </div>
              <div className="field">
                <label>Image URL</label>
                <input
                  value={eventForm.image_url}
                  onChange={(event) => setEventForm((current) => ({ ...current, image_url: event.target.value }))}
                  placeholder="https://example.com/event-image.jpg"
                />
              </div>
              <button className="primary-btn" onClick={handleCreateEvent}>Save event</button>
            </div>
          </div>

          <div className="panel" style={{ gridColumn: '1 / -1' }}>
            <SectionHeader title="Manage community content" subtitle="Delete items instantly if a campaign or image needs to be removed." />

            <div className="content-grid">
              <div>
                <h3>Gallery</h3>
                {gallery.length === 0 ? (
                  <div className="empty-state">No gallery items yet.</div>
                ) : (
                  gallery.map((item) => (
                    <div key={item.id} className="item-card">
                      <div className="item-title">{item.caption || 'Untitled'}</div>
                      <div className="item-copy">{item.image_url}</div>
                      <div className="actions-row">
                        <button className="ghost-btn" onClick={() => handleDeleteCommunity('gallery', item.id)}>Delete</button>
                      </div>
                    </div>
                  ))
                )}
              </div>
              <div>
                <h3>Events</h3>
                {events.length === 0 ? (
                  <div className="empty-state">No events yet.</div>
                ) : (
                  events.map((item) => (
                    <div key={item.id} className="item-card">
                      <div className="item-title">{item.title}</div>
                      <div className="item-copy">{item.date}</div>
                      <div className="item-copy">{item.description}</div>
                      <div className="actions-row">
                        <button className="ghost-btn" onClick={() => handleDeleteCommunity('events', item.id)}>Delete</button>
                      </div>
                    </div>
                  ))
                )}
              </div>
            </div>
          </div>
        </section>
      ) : null}

      {activeTab === 'contacts' ? (
        <section className="content-grid">
          <div className="panel">
            <SectionHeader title="Add contact manually" subtitle="Capture leads even when you want to log them directly." />
            <div className="form-grid">
              <div className="field"><label>Name</label><input value={contactForm.name} onChange={(event) => setContactForm((current) => ({ ...current, name: event.target.value }))} /></div>
              <div className="field"><label>Email</label><input type="email" value={contactForm.email} onChange={(event) => setContactForm((current) => ({ ...current, email: event.target.value }))} /></div>
              <div className="field"><label>Company</label><input value={contactForm.company} onChange={(event) => setContactForm((current) => ({ ...current, company: event.target.value }))} /></div>
              <div className="field"><label>Service</label><input value={contactForm.service} onChange={(event) => setContactForm((current) => ({ ...current, service: event.target.value }))} /></div>
              <div className="field"><label>Message</label><textarea value={contactForm.message} onChange={(event) => setContactForm((current) => ({ ...current, message: event.target.value }))} /></div>
              <button className="primary-btn" onClick={handleAddContact}>Create lead</button>
            </div>
          </div>

          <div className="panel">
            <SectionHeader title="Inbox" subtitle="Track status and keep follow-up queues clean." />
            {contacts.length === 0 ? (
              <div className="empty-state">No contact submissions yet.</div>
            ) : (
              contacts.map((item) => (
                <div key={item.id} className="item-card">
                  <div className="item-title">{item.name}</div>
                  <div className="item-copy">{item.email}</div>
                  <div className="item-copy">{item.message}</div>
                  <div className="chip-row">
                    <span className="chip">{item.status}</span>
                    {item.service ? <span className="chip">{item.service}</span> : null}
                  </div>
                  <div className="actions-row">
                    <select
                      value={item.status}
                      onChange={(event) => handleStatusChange(item.id, event.target.value as ContactSubmission['status'])}
                    >
                      <option value="new">New</option>
                      <option value="replied">Replied</option>
                      <option value="archived">Archived</option>
                    </select>
                    <button className="ghost-btn" onClick={() => handleDeleteContact(item.id)}>Delete</button>
                  </div>
                </div>
              ))
            )}
          </div>
        </section>
      ) : null}

      {activeTab === 'business-talk' ? (
        <section className="content-grid">
          <div
            className="panel"
            style={{
              gridColumn: '1 / -1',
              backgroundColor: '#ffffff',
              color: '#000000',
              border: '1px solid #000000',
            }}
          >
            <SectionHeader title="Business Talk Registrations" subtitle="View all attendees and registrations for upcoming business talk sessions." />
            <div className="actions-row">
              <button
                className="primary-btn"
                onClick={() => fetchBusinessTalkRegistrations()}
                style={{ backgroundColor: '#000000', color: '#ffffff', border: '1px solid #000000' }}
              >
                Refresh registrations
              </button>
            </div>

            {businessTalkLoading ? (
              <div className="empty-state" style={{ backgroundColor: '#ffffff', color: '#000000', border: '1px solid #000000' }}>Loading registrations...</div>
            ) : businessTalkRegistrations.length === 0 ? (
              <div className="empty-state" style={{ backgroundColor: '#ffffff', color: '#000000', border: '1px solid #000000' }}>No registrations found.</div>
            ) : (
              <div style={{ overflowX: 'auto' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse', marginTop: '1rem' }}>
                  <thead>
                    <tr style={{ borderBottom: '2px solid #000000' }}>
                      <th style={{ textAlign: 'left', padding: '0.75rem', fontWeight: '600', color: '#000000' }}>Name</th>
                      <th style={{ textAlign: 'left', padding: '0.75rem', fontWeight: '600', color: '#000000' }}>Email</th>
                      <th style={{ textAlign: 'left', padding: '0.75rem', fontWeight: '600', color: '#000000' }}>Phone</th>
                      <th style={{ textAlign: 'left', padding: '0.75rem', fontWeight: '600', color: '#000000' }}>Company</th>
                      <th style={{ textAlign: 'left', padding: '0.75rem', fontWeight: '600', color: '#000000' }}>Details</th>
                    </tr>
                  </thead>
                  <tbody>
                    {businessTalkRegistrations.map((reg, idx) => (
                      <tr key={reg.id ?? idx} style={{ borderBottom: '1px solid #000000' }}>
                        <td style={{ padding: '0.75rem', color: '#000000' }}>{reg.name || '—'}</td>
                        <td style={{ padding: '0.75rem', color: '#000000' }}>{reg.email || '—'}</td>
                        <td style={{ padding: '0.75rem', color: '#000000' }}>{reg.phone || '—'}</td>
                        <td style={{ padding: '0.75rem', color: '#000000' }}>{reg.company || '—'}</td>
                        <td style={{ padding: '0.75rem', fontSize: '0.875rem', color: '#000000' }}>
                          {Object.entries(reg)
                            .filter(([key]) => !['name', 'email', 'phone', 'company', 'id'].includes(key))
                            .map(([key, value]) => (
                              <div key={key}>
                                <strong>{key}:</strong> {String(value)}
                              </div>
                            ))}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}

            <div style={{ marginTop: '1.5rem', padding: '1rem', backgroundColor: '#ffffff', borderRadius: '0.5rem', border: '1px solid #000000' }}>
              <p className="subtle" style={{ color: '#000000' }}><strong>Total registrations:</strong> {businessTalkRegistrations.length}</p>
            </div>
          </div>
        </section>
      ) : null}

      {activeTab === 'about' ? (
        <section className="content-grid">
          <div className="panel">
            <SectionHeader title="About editor" subtitle="Update the hero, mission, and vision statements for the public page." />
            <div className="form-grid">
              <div className="field"><label>Hero title</label><input value={about.hero_title} onChange={(event) => setAbout((current) => ({ ...current, hero_title: event.target.value }))} /></div>
              <div className="field"><label>Hero subtitle</label><input value={about.hero_subtitle} onChange={(event) => setAbout((current) => ({ ...current, hero_subtitle: event.target.value }))} /></div>
              <div className="field"><label>Hero description</label><textarea value={about.hero_description} onChange={(event) => setAbout((current) => ({ ...current, hero_description: event.target.value }))} /></div>
              <div className="field"><label>Mission</label><textarea value={about.mission} onChange={(event) => setAbout((current) => ({ ...current, mission: event.target.value }))} /></div>
              <div className="field"><label>Vision</label><textarea value={about.vision} onChange={(event) => setAbout((current) => ({ ...current, vision: event.target.value }))} /></div>
              <button className="primary-btn" onClick={handleSaveAbout}>Save about content</button>
            </div>
          </div>

          <div className="panel">
            <SectionHeader title="Preview" subtitle="This is how the current content will appear on the about page." />
            <h3>{about.hero_title || 'Hero title'}</h3>
            <p className="subtle">{about.hero_subtitle || 'Hero subtitle'}</p>
            <p>{about.hero_description || 'Add a short hero description.'}</p>
            <p><strong>Mission:</strong> {about.mission || 'Mission statement will appear here.'}</p>
            <p><strong>Vision:</strong> {about.vision || 'Vision statement will appear here.'}</p>
          </div>
        </section>
      ) : null}

      {activeTab === 'settings' ? (
        <section className="content-grid">
          <div className="panel">
            <SectionHeader title="Site settings" subtitle="Update business contact details and social links in one place." />
            <div className="form-grid">
              <div className="field"><label>Site name</label><input value={settings.site_name} onChange={(event) => setSettings((current) => ({ ...current, site_name: event.target.value }))} /></div>
              <div className="field"><label>Email</label><input value={settings.site_email} onChange={(event) => setSettings((current) => ({ ...current, site_email: event.target.value }))} /></div>
              <div className="field"><label>Phone</label><input value={settings.site_phone} onChange={(event) => setSettings((current) => ({ ...current, site_phone: event.target.value }))} /></div>
              <div className="field"><label>Address</label><input value={settings.site_address} onChange={(event) => setSettings((current) => ({ ...current, site_address: event.target.value }))} /></div>
              <div className="field"><label>Description</label><textarea value={settings.site_description} onChange={(event) => setSettings((current) => ({ ...current, site_description: event.target.value }))} /></div>
              <div className="field"><label>Facebook</label><input value={settings.social_links.facebook} onChange={(event) => setSettings((current) => ({ ...current, social_links: { ...current.social_links, facebook: event.target.value } }))} /></div>
              <div className="field"><label>Twitter</label><input value={settings.social_links.twitter} onChange={(event) => setSettings((current) => ({ ...current, social_links: { ...current.social_links, twitter: event.target.value } }))} /></div>
              <div className="field"><label>LinkedIn</label><input value={settings.social_links.linkedin} onChange={(event) => setSettings((current) => ({ ...current, social_links: { ...current.social_links, linkedin: event.target.value } }))} /></div>
              <div className="field"><label>Instagram</label><input value={settings.social_links.instagram} onChange={(event) => setSettings((current) => ({ ...current, social_links: { ...current.social_links, instagram: event.target.value } }))} /></div>
              <button className="primary-btn" onClick={handleSaveSettings}>Save settings</button>
            </div>
          </div>

          <div className="panel">
            <SectionHeader title="Live config" subtitle="A snapshot of the current public settings payload." />
            <p><strong>Name:</strong> {settings.site_name || 'Not set'}</p>
            <p><strong>Email:</strong> {settings.site_email || 'Not set'}</p>
            <p><strong>Phone:</strong> {settings.site_phone || 'Not set'}</p>
            <p><strong>Address:</strong> {settings.site_address || 'Not set'}</p>
            <p><strong>Description:</strong> {settings.site_description || 'Not set'}</p>
            <div className="chip-row">
              {Object.entries(settings.social_links).map(([key, value]) => (
                <span key={key} className="chip">{key}: {value || 'not set'}</span>
              ))}
            </div>
          </div>
        </section>
      ) : null}

      {loading ? <p className="subtle">Loading dashboard...</p> : null}
    </main>
  );
}