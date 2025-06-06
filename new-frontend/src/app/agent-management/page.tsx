'use client';

import React, { useState, useEffect } from 'react';
import { Agent, AgentService } from '@/lib/agent';
import AgentLinkManager from '@/components/ui/agent-link-manager';
import AgentAnalyticsDashboard from '@/components/ui/agent-analytics-dashboard';

export default function AgentManagementPage() {
  const [agents, setAgents] = useState<Agent[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedAgent, setSelectedAgent] = useState<Agent | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [sortBy, setSortBy] = useState<'name' | 'code' | 'success_rate' | 'placements'>('name');
  const [activeTab, setActiveTab] = useState<'links' | 'analytics'>('links');

  useEffect(() => {
    fetchAgents();
  }, []);

  const fetchAgents = async () => {
    try {
      setLoading(true);
      setError(null);
      const agentsData = await AgentService.getAllAgents();
      setAgents(agentsData);
    } catch (err: any) {
      setError(err.message);
      console.error('Failed to fetch agents:', err);
    } finally {
      setLoading(false);
    }
  };

  const filteredAndSortedAgents = agents
    .filter(agent => 
      agent.user.full_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      agent.agent_code.toLowerCase().includes(searchTerm.toLowerCase()) ||
      agent.referral_code.toLowerCase().includes(searchTerm.toLowerCase())
    )
    .sort((a, b) => {
      switch (sortBy) {
        case 'name':
          return a.user.full_name.localeCompare(b.user.full_name);
        case 'code':
          return a.agent_code.localeCompare(b.agent_code);
        case 'success_rate':
          return parseFloat(b.success_rate) - parseFloat(a.success_rate);
        case 'placements':
          return b.successful_placements - a.successful_placements;
        default:
          return 0;
      }
    });

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p className="text-gray-600">Loading agents...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="bg-red-50 border border-red-200 rounded-md p-6">
            <p className="text-red-800 mb-4">{error}</p>
            <button
              onClick={fetchAgents}
              className="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700"
            >
              Try Again
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="container mx-auto px-4 py-8">
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Agent Management</h1>
          <p className="text-gray-600">Manage agent links and track performance</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Agent List */}
          <div className="lg:col-span-1">
            <div className="bg-white border border-gray-200 rounded-lg shadow-sm">
              <div className="p-4 border-b border-gray-200">
                <h2 className="text-lg font-semibold text-gray-900 mb-4">
                  Agents ({filteredAndSortedAgents.length})
                </h2>
                
                {/* Search */}
                <div className="mb-4">
                  <input
                    type="text"
                    placeholder="Search agents..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>

                {/* Sort */}
                <div className="mb-4">
                  <select
                    value={sortBy}
                    onChange={(e) => setSortBy(e.target.value as any)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="name">Sort by Name</option>
                    <option value="code">Sort by Code</option>
                    <option value="success_rate">Sort by Success Rate</option>
                    <option value="placements">Sort by Placements</option>
                  </select>
                </div>
              </div>

              {/* Agent List */}
              <div className="max-h-96 overflow-y-auto">
                {filteredAndSortedAgents.map((agent) => (
                  <div
                    key={agent.id}
                    onClick={() => setSelectedAgent(agent)}
                    className={`p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors ${
                      selectedAgent?.id === agent.id ? 'bg-blue-50 border-blue-200' : ''
                    }`}
                  >
                    <div className="flex items-center justify-between">
                      <div className="flex-1">
                        <h3 className="font-medium text-gray-900 text-sm">
                          {agent.user.full_name}
                        </h3>
                        <p className="text-xs text-gray-500 mt-1">
                          {agent.agent_code} • {agent.referral_code}
                        </p>
                        <div className="flex items-center space-x-4 mt-2 text-xs">
                          <span className="text-green-600">
                            {agent.success_rate} success
                          </span>
                          <span className="text-blue-600">
                            {agent.successful_placements} placements
                          </span>
                        </div>
                      </div>
                      {selectedAgent?.id === agent.id && (
                        <div className="w-2 h-2 bg-blue-600 rounded-full"></div>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Agent Link Manager / Analytics */}
          <div className="lg:col-span-2">
            {selectedAgent ? (
              <div className="space-y-6">
                {/* Tab Navigation */}
                <div className="bg-white border border-gray-200 rounded-lg shadow-sm">
                  <div className="border-b border-gray-200">
                    <nav className="flex space-x-8 px-6" aria-label="Tabs">
                      <button
                        onClick={() => setActiveTab('links')}
                        className={`py-4 px-1 border-b-2 font-medium text-sm ${
                          activeTab === 'links'
                            ? 'border-blue-500 text-blue-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                        }`}
                      >
                        Link Manager
                      </button>
                      <button
                        onClick={() => setActiveTab('analytics')}
                        className={`py-4 px-1 border-b-2 font-medium text-sm ${
                          activeTab === 'analytics'
                            ? 'border-blue-500 text-blue-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                        }`}
                      >
                        Analytics
                      </button>
                    </nav>
                  </div>
                </div>

                {/* Tab Content */}
                {activeTab === 'links' ? (
                  <AgentLinkManager 
                    agent={selectedAgent} 
                    baseUrl={typeof window !== 'undefined' ? `${window.location.origin}/example-form` : undefined}
                  />
                ) : (
                  <AgentAnalyticsDashboard agent={selectedAgent} />
                )}
              </div>
            ) : (
              <div className="bg-white border border-gray-200 rounded-lg shadow-sm p-8 text-center">
                <div className="text-gray-400 mb-4">
                  <svg 
                    className="w-16 h-16 mx-auto" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path 
                      strokeLinecap="round" 
                      strokeLinejoin="round" 
                      strokeWidth={1}
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" 
                    />
                  </svg>
                </div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">
                  Select an Agent
                </h3>
                <p className="text-gray-600">
                  Choose an agent from the list to manage their links, view analytics, and track performance
                </p>
              </div>
            )}
          </div>
        </div>

        {/* Quick Stats */}
        <div className="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
          <div className="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Agents</p>
                <p className="text-2xl font-bold text-gray-900">{agents.length}</p>
              </div>
              <div className="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg className="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
              </div>
            </div>
          </div>

          <div className="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Placements</p>
                <p className="text-2xl font-bold text-gray-900">
                  {agents.reduce((sum, agent) => sum + agent.successful_placements, 0)}
                </p>
              </div>
              <div className="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg className="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>

          <div className="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Avg Success Rate</p>
                <p className="text-2xl font-bold text-gray-900">
                  {agents.length > 0 
                    ? (agents.reduce((sum, agent) => sum + parseFloat(agent.success_rate), 0) / agents.length).toFixed(1)
                    : '0'
                  }%
                </p>
              </div>
              <div className="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg className="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                  <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                </svg>
              </div>
            </div>
          </div>

          <div className="bg-white p-6 border border-gray-200 rounded-lg shadow-sm">
            <div className="flex items-center">
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-600">Total Commission</p>
                <p className="text-2xl font-bold text-gray-900">
                  Rp {agents.reduce((sum, agent) => {
                    const commission = parseFloat(agent.total_commission.replace(/[^0-9.-]/g, ''));
                    return sum + (isNaN(commission) ? 0 : commission);
                  }, 0).toLocaleString()}
                </p>
              </div>
              <div className="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg className="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582-.155.103-.346.196-.567.267z"/>
                  <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}