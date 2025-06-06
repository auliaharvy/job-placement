import React from 'react';
import { Layout, Avatar, Dropdown, Space, Typography, Button } from 'antd';
import { UserOutlined, LogoutOutlined, SettingOutlined } from '@ant-design/icons';
import type { MenuProps } from 'antd';

const { Header } = Layout;
const { Text } = Typography;

interface AppHeaderProps {
  user: any;
  onLogout: () => void;
}

const AppHeader: React.FC<AppHeaderProps> = ({ user, onLogout }) => {
  const items: MenuProps['items'] = [
    {
      key: 'profile',
      icon: <UserOutlined />,
      label: 'Profile',
    },
    {
      key: 'settings',
      icon: <SettingOutlined />,
      label: 'Settings',
    },
    {
      type: 'divider',
    },
    {
      key: 'logout',
      icon: <LogoutOutlined />,
      label: 'Logout',
      onClick: onLogout,
    },
  ];

  return (
    <Header style={{ 
      background: '#fff', 
      padding: '0 24px', 
      display: 'flex', 
      justifyContent: 'space-between',
      alignItems: 'center',
      boxShadow: '0 2px 8px rgba(0,0,0,0.1)'
    }}>
      <div style={{ display: 'flex', alignItems: 'center' }}>
        <h2 style={{ margin: 0, color: '#1890ff' }}>Job Placement System</h2>
      </div>
      
      <Space>
        <Text type="secondary">Welcome back!</Text>
        <Dropdown menu={{ items }} placement="bottomRight">
          <Button type="text" style={{ padding: '4px 8px' }}>
            <Space>
              <Avatar size="small" icon={<UserOutlined />} />
              <Text>{user?.name || user?.email}</Text>
            </Space>
          </Button>
        </Dropdown>
      </Space>
    </Header>
  );
};

export default AppHeader;
