import React from 'react';
import { Layout, Menu } from 'antd';
import { 
  DashboardOutlined, 
  UserOutlined, 
  BankOutlined, 
  FileTextOutlined,
  MessageOutlined,
  BarChartOutlined
} from '@ant-design/icons';

const { Sider } = Layout;

interface AppSidebarProps {
  collapsed: boolean;
  selectedKey: string;
  onMenuSelect: (key: string) => void;
}

const AppSidebar: React.FC<AppSidebarProps> = ({ collapsed, selectedKey, onMenuSelect }) => {
  const menuItems = [
    {
      key: 'dashboard',
      icon: <DashboardOutlined />,
      label: 'Dashboard',
    },
    {
      key: 'applicants',
      icon: <UserOutlined />,
      label: 'Applicants',
    },
    {
      key: 'companies',
      icon: <BankOutlined />,
      label: 'Companies',
    },
    {
      key: 'jobs',
      icon: <FileTextOutlined />,
      label: 'Job Postings',
    },
    {
      key: 'whatsapp',
      icon: <MessageOutlined />,
      label: 'WhatsApp',
    },
    {
      key: 'reports',
      icon: <BarChartOutlined />,
      label: 'Reports',
    },
  ];

  return (
    <Sider 
      trigger={null} 
      collapsible 
      collapsed={collapsed}
      style={{
        background: '#fff',
        boxShadow: '2px 0 8px rgba(0,0,0,0.1)'
      }}
    >
      <div style={{ 
        height: 32, 
        margin: 16, 
        background: 'rgba(24, 144, 255, 0.1)',
        borderRadius: 4,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center'
      }}>
        {!collapsed && <span style={{ color: '#1890ff', fontWeight: 'bold' }}>JPS</span>}
      </div>
      
      <Menu
        mode="inline"
        selectedKeys={[selectedKey]}
        items={menuItems}
        onClick={({ key }) => onMenuSelect(key)}
        style={{ borderRight: 0 }}
      />
    </Sider>
  );
};

export default AppSidebar;
