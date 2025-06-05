import React, { useState } from 'react';
import { Card, Form, Input, Button, Typography, Space, Divider, message, Row, Col } from 'antd';
import { UserOutlined, LockOutlined, EyeInvisibleOutlined, EyeTwoTone } from '@ant-design/icons';
import { useRouter } from 'next/router';

const { Title, Text } = Typography;

interface LoginForm {
  email: string;
  password: string;
}

const LoginPage: React.FC = () => {
  const [loading, setLoading] = useState(false);
  const [form] = Form.useForm();
  const router = useRouter();

  const handleLogin = async (values: LoginForm) => {
    try {
      setLoading(true);
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // Mock authentication - replace with actual API call
      if (values.email === 'admin@jobplacement.com' && values.password === 'admin123') {
        // Store token and user info
        localStorage.setItem('token', 'mock-jwt-token');
        localStorage.setItem('user', JSON.stringify({
          id: '1',
          full_name: 'Sari Wijaya',
          email: 'admin@jobplacement.com',
          role: 'hr_staff',
          profile_picture: null,
        }));
        
        message.success('Login berhasil!');
        router.push('/dashboard');
      } else {
        message.error('Email atau password salah');
      }
    } catch (error) {
      message.error('Terjadi kesalahan saat login');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{
      minHeight: '100vh',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      padding: '20px',
    }}>
      <Row justify="center" style={{ width: '100%', maxWidth: 1200 }}>
        <Col xs={24} sm={20} md={16} lg={12} xl={8}>
          <Card
            style={{
              boxShadow: '0 10px 30px rgba(0, 0, 0, 0.1)',
              borderRadius: '16px',
              border: 'none',
            }}
          >
            <div style={{ textAlign: 'center', marginBottom: 32 }}>
              <Title level={2} style={{ color: '#1890ff', marginBottom: 8 }}>
                Job Placement System
              </Title>
              <Text type="secondary">
                Masuk ke dashboard administrator
              </Text>
            </div>

            <Form
              form={form}
              layout="vertical"
              onFinish={handleLogin}
              size="large"
            >
              <Form.Item
                name="email"
                label="Email"
                rules={[
                  { required: true, message: 'Email wajib diisi' },
                  { type: 'email', message: 'Format email tidak valid' }
                ]}
              >
                <Input
                  prefix={<UserOutlined />}
                  placeholder="Masukkan email Anda"
                />
              </Form.Item>

              <Form.Item
                name="password"
                label="Password"
                rules={[
                  { required: true, message: 'Password wajib diisi' },
                  { min: 6, message: 'Password minimal 6 karakter' }
                ]}
              >
                <Input.Password
                  prefix={<LockOutlined />}
                  placeholder="Masukkan password Anda"
                  iconRender={(visible) => (visible ? <EyeTwoTone /> : <EyeInvisibleOutlined />)}
                />
              </Form.Item>

              <Form.Item style={{ marginBottom: 16 }}>
                <Button
                  type="primary"
                  htmlType="submit"
                  loading={loading}
                  style={{ width: '100%', height: 48 }}
                >
                  Masuk
                </Button>
              </Form.Item>
            </Form>

            <Divider>
              <Text type="secondary" style={{ fontSize: 12 }}>
                Demo Login
              </Text>
            </Divider>

            <div style={{ textAlign: 'center' }}>
              <Space direction="vertical" size="small">
                <Text type="secondary" style={{ fontSize: 12 }}>
                  Email: admin@jobplacement.com
                </Text>
                <Text type="secondary" style={{ fontSize: 12 }}>
                  Password: admin123
                </Text>
              </Space>
            </div>

            <div style={{ textAlign: 'center', marginTop: 24 }}>
              <Text type="secondary" style={{ fontSize: 12 }}>
                © 2024 Job Placement System. All rights reserved.
              </Text>
            </div>
          </Card>
        </Col>
      </Row>
    </div>
  );
};

export default LoginPage;
