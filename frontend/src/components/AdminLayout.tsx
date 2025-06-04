import React, { useState } from 'react';
import {
  Layout,
  Menu,
  Button,
  Avatar,
  Dropdown,
  Badge,
  Space,
  Typography,
  theme,
} from 'antd';
import {
  DashboardOutlined,
  UserOutlined,
  BriefcaseOutlined,
  FileTextOutlined,
  TeamOutlined,
  BankOutlined,
  BarChartOutlined,
  SettingOutlined,
  BellOutlined,
  LogoutOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  WhatsAppOutlined,
} from '@ant-design/icons';
import { useRouter } from 'next/router';

const { Header, Sider, Content } = Layout;
const { Text } = Typography;

interface AdminLayoutProps {
  children: React.ReactNode;
  user?: {
    full_name: string;
    role: string;
    profile_picture?: string;
  };
}

const AdminLayout: React.FC<AdminLayoutProps> = ({ children, user }) => {
  const [collapsed, setCollapsed] = useState(false);
  const [selectedKeys, setSelectedKeys] = useState(['dashboard']);
  const router = useRouter();
  const { token } = theme.useToken();

  // Menu items based on user role
  const getMenuItems = (userRole: string) => {
    const baseItems = [
      {
        key: 'dashboard',
        icon: <DashboardOutlined />,
        label: 'Dashboard',
        onClick: () => router.push('/dashboard'),
      },
    ];

    const adminItems = [
      {
        key: 'applicants',
        icon: <UserOutlined />,
        label: 'Pelamar',
        children: [
          {
            key: 'applicants-list',
            label: 'Daftar Pelamar',
            onClick: () => router.push('/applicants'),
          },
          {
            key: 'applicants-registration',
            label: 'QR Pendaftaran',
            onClick: () => router.push('/applicants/qr-registration'),
          },
          {
            key: 'applicants-statistics',
            label: 'Statistik',
            onClick: () => router.push('/applicants/statistics'),
          },
        ],
      },
      {
        key: 'jobs',
        icon: <BriefcaseOutlined />,
        label: 'Lowongan Kerja',
        children: [
          {
            key: 'jobs-list',
            label: 'Daftar Lowongan',
            onClick: () => router.push('/jobs'),
          },
          {
            key: 'jobs-create',
            label: 'Buat Lowongan',
            onClick: () => router.push('/jobs/create'),
          },
          {
            key: 'jobs-statistics',
            label: 'Statistik',
            onClick: () => router.push('/jobs/statistics'),
          },
        ],
      },
      {
        key: 'applications',
        icon: <FileTextOutlined />,
        label: 'Lamaran',
        children: [
          {
            key: 'applications-pipeline',
            label: 'Pipeline Seleksi',
            onClick: () => router.push('/applications'),
          },
          {
            key: 'applications-review',
            label: 'Review Lamaran',
            onClick: () => router.push('/applications/review'),
          },
        ],
      },
      {
        key: 'placements',
        icon: <TeamOutlined />,
        label: 'Penempatan',
        children: [
          {
            key: 'placements-list',
            label: 'Daftar Penempatan',
            onClick: () => router.push('/placements'),
          },
          {
            key: 'placements-expiring',
            label: 'Kontrak Berakhir',
            onClick: () => router.push('/placements/expiring'),
          },
        ],
      },
      {
        key: 'companies',
        icon: <BankOutlined />,
        label: 'Perusahaan',
        onClick: () => router.push('/companies'),
      },
      {
        key: 'agents',
        icon: <TeamOutlined />,
        label: 'Agent',
        children: [
          {
            key: 'agents-list',
            label: 'Daftar Agent',
            onClick: () => router.push('/agents'),
          },
          {
            key: 'agents-leaderboard',
            label: 'Leaderboard',
            onClick: () => router.push('/agents/leaderboard'),
          },
        ],
      },
      {
        key: 'whatsapp',
        icon: <WhatsAppOutlined />,
        label: 'WhatsApp',
        children: [
          {
            key: 'whatsapp-logs',
            label: 'Log Pesan',
            onClick: () => router.push('/whatsapp/logs'),
          },
          {
            key: 'whatsapp-broadcast',
            label: 'Broadcast',
            onClick: () => router.push('/whatsapp/broadcast'),
          },
        ],
      },
      {
        key: 'analytics',
        icon: <BarChartOutlined />,
        label: 'Analytics',
        onClick: () => router.push('/analytics'),
      },
    ];

    const superAdminItems = [
      {
        key: 'settings',
        icon: <SettingOutlined />,
        label: 'Pengaturan',
        children: [
          {
            key: 'settings-users',
            label: 'Manajemen User',
            onClick: () => router.push('/settings/users'),
          },
          {
            key: 'settings-system',
            label: 'Sistem',
            onClick: () => router.push('/settings/system'),
          },
        ],
      },
    ];

    // Role-based menu
    switch (userRole) {
      case 'super_admin':
        return [...baseItems, ...adminItems, ...superAdminItems];
      case 'direktur':
      case 'hr_staff':
        return [...baseItems, ...adminItems];
      case 'agent':
        return [
          ...baseItems,
          {
            key: 'my-referrals',
            icon: <UserOutlined />,
            label: 'Referral Saya',
            onClick: () => router.push('/agent/referrals'),
          },
          {
            key: 'my-performance',
            icon: <BarChartOutlined />,
            label: 'Performa Saya',
            onClick: () => router.push('/agent/performance'),
          },
        ];
      case 'applicant':
        return [
          ...baseItems,
          {
            key: 'my-profile',
            icon: <UserOutlined />,
            label: 'Profil Saya',
            onClick: () => router.push('/applicant/profile'),
          },
          {
            key: 'my-applications',
            icon: <FileTextOutlined />,
            label: 'Lamaran Saya',
            onClick: () => router.push('/applicant/applications'),
          },
          {
            key: 'job-opportunities',
            icon: <BriefcaseOutlined />,
            label: 'Lowongan Tersedia',
            onClick: () => router.push('/applicant/jobs'),
          },
        ];
      default:
        return baseItems;
    }
  };

  const handleLogout = () => {
    // Implement logout logic
    localStorage.removeItem('token');
    router.push('/login');
  };

  const userMenuItems = [
    {
      key: 'profile',
      icon: <UserOutlined />,
      label: 'Profil',
      onClick: () => router.push('/profile'),
    },
    {
      key: 'settings',
      icon: <SettingOutlined />,
      label: 'Pengaturan',
      onClick: () => router.push('/settings'),
    },
    {
      type: 'divider' as const,
    },
    {
      key: 'logout',
      icon: <LogoutOutlined />,
      label: 'Logout',
      onClick: handleLogout,
    },
  ];

  const menuItems = getMenuItems(user?.role || 'applicant');

  return (
    <Layout style={{ minHeight: '100vh' }}>
      <Sider 
        trigger={null} 
        collapsible 
        collapsed={collapsed}
        style={{
          background: token.colorBgContainer,
          borderRight: `1px solid ${token.colorBorderSecondary}`,
        }}
        width={280}
      >
        <div 
          style={{ 
            height: 64, 
            margin: 16, 
            display: 'flex', 
            alignItems: 'center',
            justifyContent: collapsed ? 'center' : 'flex-start',
            fontSize: 18,
            fontWeight: 'bold',
            color: token.colorPrimary,
          }}
        >
          {collapsed ? 'JPS' : 'Job Placement System'}
        </div>
        
        <Menu
          mode="inline"
          selectedKeys={selectedKeys}
          style={{ borderRight: 0 }}
          items={menuItems}
          onSelect={({ key }) => setSelectedKeys([key])}
        />
      </Sider>
      
      <Layout>
        <Header 
          style={{ 
            padding: 0, 
            background: token.colorBgContainer,
            borderBottom: `1px solid ${token.colorBorderSecondary}`,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
          }}
        >
          <Button
            type="text"
            icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
            onClick={() => setCollapsed(!collapsed)}
            style={{
              fontSize: '16px',
              width: 64,
              height: 64,
            }}
          />
          
          <Space style={{ marginRight: 24 }}>
            <Badge count={5} size="small">
              <Button
                type="text"
                icon={<BellOutlined />}
                style={{ fontSize: '16px' }}
              />
            </Badge>
            
            <Dropdown
              menu={{ items: userMenuItems }}
              placement="bottomRight"
              arrow
            >
              <Space style={{ cursor: 'pointer' }}>
                <Avatar 
                  src={user?.profile_picture} 
                  icon={<UserOutlined />}
                  size="small"
                />
                <div style={{ display: collapsed ? 'none' : 'block' }}>
                  <Text strong>{user?.full_name || 'User'}</Text>
                  <br />
                  <Text type="secondary" style={{ fontSize: 12 }}>
                    {user?.role?.replace('_', ' ').toUpperCase() || 'GUEST'}
                  </Text>
                </div>
              </Space>
            </Dropdown>
          </Space>
        </Header>
        
        <Content
          style={{
            margin: 24,
            padding: 24,
            minHeight: 280,
            background: token.colorBgContainer,
            borderRadius: token.borderRadiusLG,
          }}
        >
          {children}
        </Content>
      </Layout>
    </Layout>
  );
};

export default AdminLayout;