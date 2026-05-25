import { NextRequest, NextResponse } from 'next/server';
import { getServiceClient } from '@/lib/supabase';

export const dynamic = 'force-dynamic';

export async function GET() {
  const client = getServiceClient();
  const { data, error } = await client.from('site_settings').select('*').limit(1).single();

  if (error && error.code !== 'PGRST116') {
    return NextResponse.json({ error: error.message }, { status: 500 });
  }

  return NextResponse.json({ item: data ?? null });
}

export async function POST(request: NextRequest) {
  const payload = await request.json();
  const client = getServiceClient();

  const { data: existing } = await client.from('site_settings').select('id').limit(1).single();

  if (existing?.id) {
    const { error } = await client.from('site_settings').update(payload).eq('id', existing.id);
    if (error) return NextResponse.json({ error: error.message }, { status: 500 });
    return NextResponse.json({ ok: true });
  }

  const { error } = await client.from('site_settings').insert(payload);
  if (error) return NextResponse.json({ error: error.message }, { status: 500 });
  return NextResponse.json({ ok: true });
}
