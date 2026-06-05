'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { createBrowserClient } from '@/lib/supabase-browser';

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [message, setMessage] = useState<string | null>(null);
  const supabase = createBrowserClient();

  useEffect(() => {
    void (async () => {
      const { data } = await supabase.auth.getSession();
      if (data.session) {
        router.replace('/admin');
      }
    })();
  }, [router, supabase]);

  const handleSubmit = async () => {
    setError(null);
    setLoading(true);

    const { data, error: signInError } = await supabase.auth.signInWithPassword({
      email,
      password,
    });

    setLoading(false);

    if (signInError || !data.session) {
      setError(signInError?.message || 'Unable to sign in.');
      return;
    }

    router.replace('/admin');
  };

  return (
    <main className="app-shell">
      <div className="topbar">
        <div className="brand">
          <div className="brand-mark">C</div>
          <div>
            <h1>ComeCode Admin Login</h1>
            <p className="subtle">Sign in to access the Supabase admin dashboard.</p>
          </div>
        </div>
      </div>

      <div className="content-grid" style={{ maxWidth: 620, marginTop: 24 }}>
        <div className="panel">
          <div className="panel-header">
            <div>
              <h2>Admin sign in</h2>
              <p className="subtle">Use your admin credentials to continue.</p>
            </div>
          </div>

          {error ? <div className="alert error">{error}</div> : null}
          {message ? <div className="alert success">{message}</div> : null}

          <div className="form-grid">
            <div className="field">
              <label>Email address</label>
              <input
                type="email"
                value={email}
                onChange={(event) => setEmail(event.target.value)}
                placeholder="admin@example.com"
              />
            </div>
            <div className="field">
              <label>Password</label>
              <input
                type="password"
                value={password}
                onChange={(event) => setPassword(event.target.value)}
                placeholder="Enter your admin password"
              />
            </div>
            <button className="primary-btn" disabled={loading} onClick={handleSubmit}>
              {loading ? 'Signing in…' : 'Sign in'}
            </button>
          </div>

          <p className="subtle" style={{ marginTop: 16 }}>
            If you do not have an admin account yet, create one in your Supabase project's Authentication settings.
          </p>
        </div>
      </div>
    </main>
  );
}
