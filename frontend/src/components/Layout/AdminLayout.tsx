/**
 * File Path: /frontend/src/components/Layout/AdminLayout.tsx
 * Layout utama untuk admin panel dengan sidebar dan header
 */

import React, { useState } from 'react';
import { Layout, Menu, Avatar, Dropdown, Badge, Button } from 'antd';
import {
  DashboardOutlined,
  UserOutlined,
  BriefcaseOutlined,
  FileTextOutlined,
  TeamOutlined,
  BuildingOutlined,
  BarChartOutlined,
  SettingOutlined,
  BellOutlined,
  LogoutOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined
} from '@ant-design/icons';
import { useRouter } from 'next/router';
import Link from 'next/link';

const { Header, Sider, Content } = Layout;

interface AdminLayoutProps {
  children: React.ReactNode;
}

const AdminLayout: React.FC<AdminLayoutProps> = ({ children }) => {
  const [collapsed, setCollapsed] = useState(false);
  const router = useRouter();

  // TODO: Implement user context and authentication
  const currentUser = {
    name: 'Admin User',
    role: 'Super Admin',
    avatar: null
  };

  // TODO: Implement menu items based on user role
  const menuItems = [
    {
      key: '/dashboard',
      icon: <DashboardOutlined />,
      label: <Link href="/dashboard">Dashboard</Link>,
    },
    {
      key: '/applicants',
      icon: <UserOutlined />,
      label: 'Manajemen Pelamar',
      children: [
        {
          key: '/applicants/list',
          label: <Link href="/applicants">Daftar Pelamar</Link>,
        },
        {
          key: '/applicants/registration',
          label: <Link href="/applicants/registration">QR Code Pendaftaran</Link>,
        },
      ],
    },
    {
      key: '/jobs',
      icon: <BriefcaseOutlined />,
      label: 'Manajemen Lowongan',
      children: [
        {
          key: '/jobs/list',
          label: <Link href="/jobs">Daftar Lowongan</Link>,
        },
        {
          key: '/jobs/create',
          label: <Link href="/jobs/create">Buat Lowongan</Link>,
        },
      ],
    },
    {
      key: '/applications',
      icon: <FileTextOutlined />,
      label: 'Proses Seleksi',
      children: [
        {
          key: '/applications/list',
          label: <Link href="/applications">Daftar Aplikasi</Link>,
        },
        {
          key: '/applications/pipeline',
          label: <Link href="/applications/pipeline">Pipeline Seleksi</Link>,
        },
      ],
    },
    {
      key: '/placements',
      icon: <TeamOutlined />,
      label: 'Penempatan & Kontrak',
      children: [
        {
          key: '/placements/active',
          label: <Link href="/placements">Penempatan Aktif</Link>,
        },
        {
          key: '/placements/contracts',
          label: <Link href="/placements/contracts">Manajemen Kontrak</Link>,
        },
      ],
    },
    {
      key: '/agents',
      icon: <TeamOutlined />,
      label: <Link href="/agents">Agent & Referral</Link>,
    },
    {
      key: '/companies',
      icon: <BuildingOutlined />,
      label: <Link href="/companies">Perusahaan Klien</Link>,
    },
    {
      key: '/analytics',
      icon: <BarChartOutlined />,
      label: 'Analytics & Report',
      children: [
        {
          key: '/analytics/dashboard',
          label: <Link href="/analytics">Dashboard Eksekutif</Link>,
        },
        {
          key: '/analytics/reports',
          label: <Link href="/analytics/reports">Generator Report</Link>,
        },
      ],
    },
    {
      key: '/settings',
      icon: <SettingOutlined />,
      label: <Link href="/settings">Pengaturan</Link>,
    },
  ];

  // TODO: Implement user menu actions
  const handleLogout = () => {
    // Implement logout logic
    console.log('Logout clicked');
  };

  const userMenuItems = [
    {
      key: 'profile',
      label: 'Profil Saya',
      icon: <UserOutlined />,
    },
    {
      key: 'settings',
      label: 'Pengaturan',
      icon: <SettingOutlined />,
    },
    {
      type: 'divider',
    },
    {
      key: 'logout',
      label: 'Keluar',
      icon: <LogoutOutlined />,
      onClick: handleLogout,
    },
  ];

  return (
    <Layout style={{ minHeight: '100vh' }}>
      <Sider 
        trigger={null} 
        collapsible 
        collapsed={collapsed}
        width={256}
        theme="dark"
      >
        <div className="logo" style={{ 
          height: 64, 
          padding: '16px', 
          color: 'white', 
          fontSize: '18px',
          fontWeight: 'bold',
          textAlign: 'center',
          borderBottom: '1px solid #303030'
        }}>
          {collapsed ? 'JPS' : 'Job Placement System'}
        </div>
        
        <Menu
          theme="dark"
          mode="inline"
          selectedKeys={[router.pathname]}
          items={menuItems}
          style={{ marginTop: 0 }}
        />
      </Sider>

      <Layout>
        <Header style={{ 
          background: '#fff', 
          padding: '0 24px', 
          display: 'flex', 
          alignItems: 'center',
          justifyContent: 'space-between',
          boxShadow: '0 1px 4px rgba(0,21,41,.08)'
        }}>
          <Button
            type="text"
            icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
            onClick={() => setCollapsed(!collapsed)}
            style={{ fontSize: '16px', width: 40, height: 40 }}
          />

          <div style={{ display: 'flex', alignItems: 'center', gap: '16px' }}>
            {/* TODO: Implement notifications */}
            <Badge count={5} size="small">
              <Button type="text" icon={<BellOutlined />} size="large" />
            </Badge>

            <Dropdown
              menu={{ items: userMenuItems }}
              placement="bottomRight"
              arrow
            >
              <div style={{ 
                display: 'flex', 
                alignItems: 'center', 
                gap: '8px', 
                cursor: 'pointer',
                padding: '4px 8px',
                borderRadius: '6px'
              }}>
                <Avatar size="small" icon={<UserOutlined />} />
                <div style={{ display: 'flex', flexDirection: 'column', lineHeight: 1.2 }}>
                  <span style={{ fontWeight: 500 }}>{currentUser.name}</span>
                  <span style={{ fontSize: '12px', color: '#666' }}>{currentUser.role}</span>
                </div>
              </div>
            </Dropdown>
          </div>
        </Header>

        <Content style={{ 
          margin: '24px',
          padding: '24px',
          background: '#fff',
          borderRadius: '8px',
          minHeight: 'calc(100vh - 112px)'
        }}>
          {children}
        </Content>
      </Layout>
    </Layout>
  );
};

export default AdminLayout;
