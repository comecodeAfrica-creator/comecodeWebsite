import { NextResponse } from 'next/server';
import { getServiceClient } from '@/lib/supabase';

export const dynamic = 'force-dynamic';

export async function GET() {
  const client = getServiceClient();

  const [{ count: galleryCount }, { count: eventCount }, { count: contactCount }, { count: newContactCount }] = await Promise.all([
    client.from('gallery').select('*', { count: 'exact', head: true }),
    client.from('events').select('*', { count: 'exact', head: true }),
    client.from('contact_submissions').select('*', { count: 'exact', head: true }),
    client.from('contact_submissions').select('*', { count: 'exact', head: true }).eq('status', 'new'),
  ]);

  return NextResponse.json({
    gallery: galleryCount ?? 0,
    events: eventCount ?? 0,
    contacts: contactCount ?? 0,
    newContacts: newContactCount ?? 0,
  });
}
