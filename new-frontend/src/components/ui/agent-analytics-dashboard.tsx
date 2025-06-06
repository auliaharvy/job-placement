'use client';

import React, { useState } from 'react';
import { Agent } from '@/lib/agent';
import { useAgentAnalytics, AgentLinkAnalytics } from '@/hooks/useAgentAnalytics';

interface AgentAnalyticsDashboardProps {
  agent: Agent;
}

export default function AgentAnalyticsDashboard({ agent }: AgentAnalyticsDashboardProps) {
  const agentId = agent.id.toString();
  const { analytics, loading, refreshAnalytics, clearAnalytics, exportAnalytics } = useAgentAnalytics(agentId);
  const [showExport, setShowExport] = useState(false);

  if (loading) {
    return (
      <div className="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <div className="animate-pulse">
          <div className="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
          <div className="h-20 bg-gray-200 rounded mb-4"></div>
          <div className="h-4 bg-gray-200 rounded w-1/2"></div>
        </div>
      </div>
    );
  }

  if (!analytics) {
    return (
      <div className="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">
          Analytics - {agent.user.full_name}
        </h3>
        <div className="text-center py-8">
          <div className="text-gray-400 mb-4">
            <svg className="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <p className="text-gray-600">No analytics data available yet</p>
          <p className="text-sm text-gray-500 mt-2">Analytics will appear when people click on agent links</p>
        </div>
      </div>
    );
  }

  const handleExport = () => {
    const data = exportAnalytics();
    const blob = new Blob([data], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `agent-${agent.agent_code}-analytics.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  };

  return (
    <div className="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-lg font-semibold text-gray-900">
          Analytics - {agent.user.full_name}
        </h3>
        <div className="flex gap-2">
          <button
            onClick={refreshAnalytics}
            className="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700"
          >
            Refresh
          </button>
          <button
            onClick={handleExport}
            className="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700"
          >
            Export
          </button>
          <button
            onClick={clearAnalytics}
            className="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
          >
            Clear
          </button>
        </div>
      </div>

      {/* Overview Stats */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div className="bg-blue-50 p-4 rounded-lg">
          <div className="flex items-center">
            <div className="flex-1">
              <p className="text-sm font-medium text-blue-600">Total Clicks</p>
              <p className="text-2xl font-bold text-blue-900">{analytics.totalClicks}</p>
            </div>
            <div className="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg className="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                <path fillRule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clipRule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>

        <div className="bg-green-50 p-4 rounded-lg">
          <div className="flex items-center">
            <div className="flex-1">
              <p className="text-sm font-medium text-green-600">Unique Clicks</p>
              <p className="text-2xl font-bold text-green-900">{analytics.uniqueClicks}</p>
            </div>
            <div className="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
              <svg className="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
              </svg>
            </div>
          </div>
        </div>

        <div className="bg-purple-50 p-4 rounded-lg">
          <div className="flex items-center">
            <div className="flex-1">
              <p className="text-sm font-medium text-purple-600">Click Rate</p>
              <p className="text-2xl font-bold text-purple-900">
                {analytics.totalClicks > 0 ? ((analytics.uniqueClicks / analytics.totalClicks) * 100).toFixed(1) : '0'}%
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

        <div className="bg-yellow-50 p-4 rounded-lg">
          <div className="flex items-center">
            <div className="flex-1">
              <p className="text-sm font-medium text-yellow-600">Conversion</p>
              <p className="text-2xl font-bold text-yellow-900">
                {analytics.conversionRate ? `${analytics.conversionRate}%` : 'N/A'}
              </p>
            </div>
            <div className="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
              <svg className="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clipRule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Traffic Sources */}
        <div>
          <h4 className="text-md font-medium text-gray-900 mb-3">Traffic Sources</h4>
          <div className="space-y-2">
            {Object.entries(analytics.clicksBySource).map(([source, count]) => (
              <div key={source} className="flex items-center justify-between p-2 bg-gray-50 rounded">
                <span className="text-sm text-gray-600 capitalize">{source}</span>
                <span className="text-sm font-medium text-gray-900">{count} clicks</span>
              </div>
            ))}
            {Object.keys(analytics.clicksBySource).length === 0 && (
              <p className="text-sm text-gray-500 italic">No source data available</p>
            )}
          </div>
        </div>

        {/* Traffic Medium */}
        <div>
          <h4 className="text-md font-medium text-gray-900 mb-3">Traffic Medium</h4>
          <div className="space-y-2">
            {Object.entries(analytics.clicksByMedium).map(([medium, count]) => (
              <div key={medium} className="flex items-center justify-between p-2 bg-gray-50 rounded">
                <span className="text-sm text-gray-600 capitalize">{medium}</span>
                <span className="text-sm font-medium text-gray-900">{count} clicks</span>
              </div>
            ))}
            {Object.keys(analytics.clicksByMedium).length === 0 && (
              <p className="text-sm text-gray-500 italic">No medium data available</p>
            )}
          </div>
        </div>

        {/* Campaigns */}
        <div>
          <h4 className="text-md font-medium text-gray-900 mb-3">Campaigns</h4>
          <div className="space-y-2">
            {Object.entries(analytics.clicksByCampaign).map(([campaign, count]) => (
              <div key={campaign} className="flex items-center justify-between p-2 bg-gray-50 rounded">
                <span className="text-sm text-gray-600">{campaign}</span>
                <span className="text-sm font-medium text-gray-900">{count} clicks</span>
              </div>
            ))}
            {Object.keys(analytics.clicksByCampaign).length === 0 && (
              <p className="text-sm text-gray-500 italic">No campaign data available</p>
            )}
          </div>
        </div>

        {/* Daily Clicks */}
        <div>
          <h4 className="text-md font-medium text-gray-900 mb-3">Daily Clicks</h4>
          <div className="space-y-2 max-h-40 overflow-y-auto">
            {Object.entries(analytics.clicksByDate)
              .sort(([a], [b]) => b.localeCompare(a))
              .map(([date, count]) => (
                <div key={date} className="flex items-center justify-between p-2 bg-gray-50 rounded">
                  <span className="text-sm text-gray-600">{new Date(date).toLocaleDateString()}</span>
                  <span className="text-sm font-medium text-gray-900">{count} clicks</span>
                </div>
              ))}
            {Object.keys(analytics.clicksByDate).length === 0 && (
              <p className="text-sm text-gray-500 italic">No daily data available</p>
            )}
          </div>
        </div>
      </div>

      {/* Performance vs Agent Stats */}
      <div className="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
        <h4 className="text-md font-medium text-blue-900 mb-2">Performance Comparison</h4>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
          <div>
            <p className="text-blue-600 font-medium">Agent Success Rate</p>
            <p className="text-blue-900">{agent.success_rate}</p>
          </div>
          <div>
            <p className="text-blue-600 font-medium">Total Placements</p>
            <p className="text-blue-900">{agent.successful_placements}</p>
          </div>
          <div>
            <p className="text-blue-600 font-medium">Link Clicks</p>
            <p className="text-blue-900">{analytics.totalClicks}</p>
          </div>
          <div>
            <p className="text-blue-600 font-medium">Click-to-Placement</p>
            <p className="text-blue-900">
              {analytics.totalClicks > 0 ? ((agent.successful_placements / analytics.totalClicks) * 100).toFixed(1) : '0'}%
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}