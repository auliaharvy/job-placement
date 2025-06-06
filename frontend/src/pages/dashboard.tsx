import React, { useState, useEffect } from 'react';
import { Layout, Button, Row, Col, Card, Statistic, Typography, Space, Spin } from 'antd';
import { 
  MenuUnfoldOutlined, 
  MenuFoldOutlined,
  UserOutlined,
  BankOutlined,
  FileTextOutlined,
  CheckCircleOutlined
} from '@ant-design/icons';
import { useRouter } from 'next/router';
import { useAuth } from '../hooks/useAuth';
import AppHeader from '../components/AppHeader';
import AppSidebar from '../components/AppSidebar';

const { Content } = Layout;
const { Title, Text } = Typography;

const DashboardPage: React.FC = () => {
  const [collapsed, setCollapsed] = useState(false);
  const [selectedMenu, setSelectedMenu] = useState('dashboard');
  const { user, isAuthenticated, isLoading, logout } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!isLoading && !isAuthenticated) {
      router.push('/login');
    }
  }, [isAuthenticated, isLoading, router]);

  const handleLogout = async () => {
    await logout();
    router.push('/login');
  };

  const handleMenuSelect = (key: string) => {
    setSelectedMenu(key);
    // For now, we'll just update the selected menu
    // Later you can add routing to different pages
  };

  if (isLoading) {
    return (
      <div style={{ 
        height: '100vh', 
        display: 'flex', 
        alignItems: 'center', 
        justifyContent: 'center' 
      }}>
        <Spin size="large" />
      </div>
    );
  }

  if (!isAuthenticated) {
    return null; // Will redirect to login
  }

  const renderDashboardContent = () => {
    switch (selectedMenu) {
      case 'dashboard':
        return (
          <div>
            <Title level={3}>Dashboard Overview</Title>
            <Row gutter={[16, 16]} style={{ marginBottom: 24 }}>
              <Col xs={24} sm={12} md={6}>
                <Card>
                  <Statistic
                    title="Total Applicants"
                    value={1234}
                    prefix={<UserOutlined />}
                    valueStyle={{ color: '#1890ff' }}
                  />
                </Card>
              </Col>
              <Col xs={24} sm={12} md={6}>
                <Card>
                  <Statistic
                    title="Active Companies"
                    value={56}
                    prefix={<BankOutlined />}
                    valueStyle={{ color: '#52c41a' }}
                  />
                </Card>
              </Col>
              <Col xs={24} sm={12} md={6}>
                <Card>
                  <Statistic
                    title="Job Postings"
                    value={89}
                    prefix={<FileTextOutlined />}
                    valueStyle={{ color: '#722ed1' }}
                  />
                </Card>
              </Col>
              <Col xs={24} sm={12} md={6}>
                <Card>
                  <Statistic
                    title="Successful Placements"
                    value={321}
                    prefix={<CheckCircleOutlined />}
                    valueStyle={{ color: '#fa8c16' }}
                  />
                </Card>
              </Col>
            </Row>

            <Row gutter={[16, 16]}>
              <Col xs={24} lg={12}>
                <Card title="Recent Activities" size="small">
                  <Space direction="vertical" style={{ width: '100%' }}>
                    <Text>• New applicant registered: John Doe</Text>
                    <Text>• Job posting created: Software Developer at PT ABC</Text>
                    <Text>• Application submitted for Marketing Manager</Text>
                    <Text>• Interview scheduled for Jane Smith</Text>
                    <Text>• Placement completed: Alice Johnson at PT XYZ</Text>
                  </Space>
                </Card>
              </Col>
              <Col xs={24} lg={12}>
                <Card title="System Status" size="small">
                  <Space direction="vertical" style={{ width: '100%' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                      <Text>Database Connection:</Text>
                      <Text style={{ color: '#52c41a' }}>✓ Online</Text>
                    </div>
                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                      <Text>WhatsApp Service:</Text>
                      <Text style={{ color: '#52c41a' }}>✓ Connected</Text>
                    </div>
                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                      <Text>Email Service:</Text>
                      <Text style={{ color: '#52c41a' }}>✓ Active</Text>
                    </div>
                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                      <Text>File Storage:</Text>
                      <Text style={{ color: '#52c41a' }}>✓ Available</Text>
                    </div>
                  </Space>
                </Card>
              </Col>
            </Row>
          </div>
        );
      
      case 'applicants':
        return (
          <div>
            <Title level={3}>Applicants Management</Title>
            <Card>
              <Text>Applicants management functionality will be implemented here.</Text>
            </Card>
          </div>
        );
      
      case 'companies':
        return (
          <div>
            <Title level={3}>Companies Management</Title>
            <Card>
              <Text>Companies management functionality will be implemented here.</Text>
            </Card>
          </div>
        );
      
      case 'jobs':
        return (
          <div>
            <Title level={3}>Job Postings</Title>
            <Card>
              <Text>Job postings management functionality will be implemented here.</Text>
            </Card>
          </div>
        );
      
      case 'whatsapp':
        return (
          <div>
            <Title level={3}>WhatsApp Management</Title>
            <Card>
              <Text>WhatsApp integration and messaging functionality will be implemented here.</Text>
            </Card>
          </div>
        );
      
      case 'reports':
        return (
          <div>
            <Title level={3}>Reports & Analytics</Title>
            <Card>
              <Text>Reports and analytics functionality will be implemented here.</Text>
            </Card>
          </div>
        );
      
      default:
        return (
          <div>
            <Title level={3}>Page Not Found</Title>
            <Card>
              <Text>The requested page is not available.</Text>
            </Card>
          </div>
        );
    }
  };

  return (
    <Layout style={{ minHeight: '100vh' }}>
      <AppSidebar 
        collapsed={collapsed}
        selectedKey={selectedMenu}
        onMenuSelect={handleMenuSelect}
      />
      
      <Layout>
        <AppHeader user={user} onLogout={handleLogout} />
        
        <Content style={{ margin: '24px 16px 0', overflow: 'initial' }}>
          <div style={{ 
            display: 'flex', 
            alignItems: 'center', 
            marginBottom: 16 
          }}>
            <Button
              type="text"
              icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
              onClick={() => setCollapsed(!collapsed)}
              style={{
                fontSize: '16px',
                width: 40,
                height: 40,
              }}
            />
            <Title level={4} style={{ margin: 0, marginLeft: 8 }}>
              {selectedMenu.charAt(0).toUpperCase() + selectedMenu.slice(1)}
            </Title>
          </div>
          
          <div style={{
            padding: 24,
            background: '#fff',
            borderRadius: 8,
            boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
            minHeight: 'calc(100vh - 200px)'
          }}>
            {renderDashboardContent()}
          </div>
        </Content>
      </Layout>
    </Layout>
  );
};

export default DashboardPage;
