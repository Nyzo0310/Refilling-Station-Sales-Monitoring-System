import React from 'react';
import ReactDOM from 'react-dom/client';
import { SpeedInsights } from '@vercel/speed-insights/react';
import '../css/app.css';

function App() {
  return (
    <div style={{ minHeight: '100vh', display: 'flex', justifyContent: 'center', alignItems: 'center', background: '#f3f4f6' }}>
      <SpeedInsights />
      <div style={{ background: 'white', padding: '24px', borderRadius: '12px', boxShadow: '0 10px 25px rgba(0,0,0,0.08)', maxWidth: '640px', width: '100%' }}>
        <h1 style={{ fontSize: '24px', fontWeight: '700', marginBottom: '8px' }}>
          Refilling Station Monitoring System
        </h1>
        <p style={{ color: '#4b5563', marginBottom: '4px' }}>
          Base setup is working.
        </p>
        <p style={{ color: '#6b7280', fontSize: '14px' }}>
          Backend: Laravel · Frontend: React · Database: MySQL (XAMPP)
        </p>
      </div>
    </div>
  );
}

const el = document.getElementById('app');
if (el) {
  ReactDOM.createRoot(el).render(<App />);
}

