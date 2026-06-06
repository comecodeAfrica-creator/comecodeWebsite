export async function GET() {
  const response = await fetch('https://businesstalkexpress.onrender.com/registrations', {
    headers: {
      'x-api-key': process.env.BUSINESS_TALK_API_KEY!,
    },
  });

  const data = await response.json();
  return Response.json(data);
}