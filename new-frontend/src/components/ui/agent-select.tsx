'use client';

import React, { useState, useEffect } from 'react';
import { Agent, AgentService } from '@/lib/agent';

interface AgentSelectProps {
  value?: string;
  onChange: (agentId: string, agent: Agent | null) => void;
  placeholder?: string;
  className?: string;
  disabled?: boolean;
}

export default function AgentSelect({ 
  value, 
  onChange, 
  placeholder = "Pilih Agent",
  className = "",
  disabled = false 
}: AgentSelectProps) {
  const [agents, setAgents] = useState<Agent[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

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

  const handleSelectChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const selectedAgentId = e.target.value;
    const selectedAgent = agents.find(agent => agent.id.toString() === selectedAgentId) || null;
    onChange(selectedAgentId, selectedAgent);
  };

  if (loading) {
    return (
      <select 
        disabled 
        className={`w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 ${className}`}
      >
        <option>Loading agents...</option>
      </select>
    );
  }

  if (error) {
    return (
      <div className="space-y-2">
        <select 
          disabled 
          className={`w-full px-3 py-2 border border-red-300 rounded-md bg-red-50 ${className}`}
        >
          <option>Error loading agents</option>
        </select>
        <p className="text-sm text-red-600">{error}</p>
        <button 
          type="button"
          onClick={fetchAgents}
          className="text-sm text-blue-600 hover:text-blue-800 underline"
        >
          Try again
        </button>
      </div>
    );
  }

  return (
    <select
      value={value || ''}
      onChange={handleSelectChange}
      disabled={disabled}
      className={`w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent ${
        disabled ? 'bg-gray-50 cursor-not-allowed' : 'bg-white'
      } ${className}`}
    >
      <option value="">{placeholder}</option>
      {agents.map((agent) => (
        <option key={agent.id} value={agent.id.toString()}>
          {agent.user.full_name} ({agent.agent_code}) - {agent.referral_code}
        </option>
      ))}
    </select>
  );
}