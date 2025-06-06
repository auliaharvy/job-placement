# Agent Link Management System

Sistem manajemen link agent yang memungkinkan:
1. **Form dengan Auto-fill Agent** - Form yang otomatis memilih agent berdasarkan parameter URL
2. **Link Generator** - Membuat berbagai jenis link untuk agent
3. **Analytics Tracking** - Melacak klik dan performa link agent

## Fitur Utama

### 1. Agent Select Component
Komponen dropdown yang mengambil data agent dari API:
```tsx
import AgentSelect from '@/components/ui/agent-select';

<AgentSelect
  value={selectedAgentId}
  onChange={(agentId, agent) => handleAgentChange(agentId, agent)}
  placeholder="Pilih Agent"
/>
```

### 2. Auto-fill Agent dari URL
Hook yang otomatis mengisi agent berdasarkan parameter URL:
```tsx
import { useAgentAutoFill } from '@/hooks/useAgentAutoFill';

const {
  selectedAgentId,
  selectedAgent,
  loading,
  error,
  setSelectedAgent,
  generateAgentLink
} = useAgentAutoFill();
```

**Parameter URL yang didukung:**
- `?agent=123` - Menggunakan ID agent
- `?ref=ABC123` - Menggunakan referral code

### 3. Link Generator
Utility untuk membuat berbagai jenis link:
```tsx
import { AgentLinkGenerator } from '@/lib/agent-link-generator';

// Link dengan UTM parameters
const socialLink = AgentLinkGenerator.generateAgentLink({
  agentId: '123',
  utmSource: 'facebook',
  utmMedium: 'social',
  utmCampaign: 'recruitment'
});

// Share links untuk platform sosial
const whatsappLink = AgentLinkGenerator.generateWhatsAppShareLink(agentLink);
const telegramLink = AgentLinkGenerator.generateTelegramShareLink(agentLink);
```

### 4. Analytics Tracking
Hook untuk melacak klik dan analytics:
```tsx
import { useAgentAnalytics } from '@/hooks/useAgentAnalytics';

const {
  analytics,
  trackClick,
  refreshAnalytics,
  clearAnalytics,
  exportAnalytics
} = useAgentAnalytics(agentId);
```

## Struktur File

```
src/
├── components/
│   ├── ui/
│   │   ├── agent-select.tsx              # Dropdown select agent
│   │   ├── agent-link-manager.tsx        # Manajemen link agent
│   │   └── agent-analytics-dashboard.tsx # Dashboard analytics
│   └── forms/
│       └── example-form.tsx              # Contoh form dengan auto-fill
├── hooks/
│   ├── useAgentAutoFill.ts              # Hook auto-fill agent
│   └── useAgentAnalytics.ts             # Hook analytics tracking
├── lib/
│   ├── agent.ts                         # Service API agent
│   └── agent-link-generator.ts          # Utility generator link
└── app/
    ├── example-form/                    # Halaman contoh form
    └── agent-management/                # Halaman manajemen agent
```

## Cara Penggunaan

### 1. Setup Form dengan Auto-fill Agent

```tsx
'use client';

import React, { useState } from 'react';
import AgentSelect from '@/components/ui/agent-select';
import { useAgentAutoFill } from '@/hooks/useAgentAutoFill';

export default function MyForm() {
  const {
    selectedAgentId,
    selectedAgent,
    setSelectedAgent
  } = useAgentAutoFill();

  const [formData, setFormData] = useState({
    name: '',
    email: '',
    agentId: selectedAgentId
  });

  // Update form ketika agent auto-filled
  React.useEffect(() => {
    if (selectedAgentId) {
      setFormData(prev => ({ ...prev, agentId: selectedAgentId }));
    }
  }, [selectedAgentId]);

  return (
    <form>
      {/* Field lain */}
      
      <AgentSelect
        value={formData.agentId}
        onChange={(agentId, agent) => {
          setSelectedAgent(agentId, agent);
          setFormData(prev => ({ ...prev, agentId }));
        }}
      />
      
      {selectedAgent && (
        <div>
          ✓ Agent: {selectedAgent.user.full_name}
        </div>
      )}
    </form>
  );
}
```

### 2. Generate Link untuk Agent

```tsx
import { AgentLinkGenerator } from '@/lib/agent-link-generator';

// Generate semua varian link
const linkVariants = AgentLinkGenerator.generateAgentLinkVariants(
  agentId, 
  referralCode, 
  'https://yoursite.com/form'
);

console.log(linkVariants);
// {
//   agentLink: "https://yoursite.com/form?agent=123",
//   referralLink: "https://yoursite.com/form?ref=ABC123",
//   socialMediaLink: "https://yoursite.com/form?agent=123&utm_source=social",
//   emailLink: "https://yoursite.com/form?agent=123&utm_source=email",
//   whatsappLink: "https://yoursite.com/form?agent=123&utm_source=whatsapp"
// }
```

### 3. Track Analytics

```tsx
import { useAgentAnalytics } from '@/hooks/useAgentAnalytics';

const { analytics, trackClick } = useAgentAnalytics();

// Manual tracking (otomatis dilakukan di useAgentAutoFill)
trackClick('123', 'ABC123');

// Analytics data
console.log(analytics);
// {
//   totalClicks: 50,
//   uniqueClicks: 35,
//   clicksBySource: { facebook: 20, email: 15, direct: 15 },
//   clicksByMedium: { social: 25, email: 15, direct: 10 },
//   clicksByDate: { "2025-06-06": 10, "2025-06-05": 8 }
// }
```

## API Endpoints

Pastikan API endpoints berikut tersedia:

```typescript
// GET /api/agents - Ambil semua agent
interface Agent {
  id: number;
  agent_code: string;
  referral_code: string;
  level: string;
  total_referrals: number;
  successful_placements: number;
  success_rate: string;
  total_points: number;
  total_commission: string;
  user: {
    id: number;
    full_name: string;
    email: string;
    phone: string;
  };
}

// GET /api/agents/referral/{code} - Cari agent berdasarkan referral code
```

## Contoh URL Links

### Link dengan Agent ID
```
https://yoursite.com/form?agent=123
```

### Link dengan Referral Code
```
https://yoursite.com/form?ref=ABC123
```

### Link dengan UTM Tracking
```
https://yoursite.com/form?agent=123&utm_source=facebook&utm_medium=social&utm_campaign=recruitment_june
```

### WhatsApp Share Link
```
https://wa.me/?text=Halo!%20Saya%20ingin%20membantu%20Anda%20dalam%20pencarian%20kerja.%20Silakan%20klik%20link%20berikut%3A%20https%3A//yoursite.com/form%3Fagent%3D123
```

## QR Code

QR Code otomatis generated untuk setiap link agent menggunakan `https://api.qrserver.com/v1/create-qr-code/`

## Analytics Storage

Analytics disimpan di localStorage untuk demo. Untuk production, implementasikan:

1. API endpoint untuk menyimpan analytics
2. Database untuk menyimpan click events
3. Dashboard analytics yang lebih lengkap

## Testing

### Test Auto-fill
1. Kunjungi `/example-form?agent=123`
2. Agent harus otomatis terpilih
3. Form harus menampilkan info agent

### Test Link Generation
1. Kunjungi `/agent-management`
2. Pilih agent
3. Generate berbagai jenis link
4. Test setiap link

### Test Analytics
1. Klik link agent
2. Periksa analytics dashboard
3. Verify tracking data

## Environment Variables

```env
NEXT_PUBLIC_API_BASE_URL=https://your-api.com/api
```

## Dependencies

```json
{
  "dependencies": {
    "next": "^14.0.0",
    "react": "^18.0.0",
    "tailwindcss": "^3.0.0"
  }
}
```