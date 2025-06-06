import React, { useState, useEffect } from 'react';
import { UseFormRegister, FieldErrors, Control, useFieldArray, UseFormSetValue } from 'react-hook-form';
import { Plus, X, UserCheck } from 'lucide-react';
import { ApplicantRegistrationData } from '@/lib/registration';
import { AgentService, Agent } from '@/lib/agent';

interface JobPreferencesStepProps {
  register: UseFormRegister<ApplicantRegistrationData>;
  errors: FieldErrors<ApplicantRegistrationData>;
  control: Control<ApplicantRegistrationData>;
  setValue: UseFormSetValue<ApplicantRegistrationData>;
  selectedAgentId?: number;
}

export default function JobPreferencesStep({ 
  register, 
  errors, 
  control, 
  setValue,
  selectedAgentId 
}: JobPreferencesStepProps) {
  const [agents, setAgents] = useState<Agent[]>([]);
  const [loadingAgents, setLoadingAgents] = useState(true);
  const [selectedAgent, setSelectedAgent] = useState<Agent | null>(null);

  const { fields: positionFields, append: appendPosition, remove: removePosition } = useFieldArray({
    control,
    name: 'preferred_positions'
  });

  const { fields: locationFields, append: appendLocation, remove: removeLocation } = useFieldArray({
    control,
    name: 'preferred_locations'
  });

  // Load agents on component mount
  useEffect(() => {
    const loadAgents = async () => {
      try {
        const agentData = await AgentService.getAllAgents();
        setAgents(agentData);
        
        // If there's a selectedAgentId, set it and find the agent details
        if (selectedAgentId) {
          const agent = agentData.find(a => a.id === selectedAgentId);
          if (agent) {
            setSelectedAgent(agent);
            setValue('agent_id', selectedAgentId);
          }
        }
      } catch (error) {
        console.error('Failed to load agents:', error);
      } finally {
        setLoadingAgents(false);
      }
    };

    loadAgents();
  }, [selectedAgentId, setValue]);

  const handleAgentChange = (agentId: string) => {
    if (agentId) {
      const agent = agents.find(a => a.id === parseInt(agentId));
      setSelectedAgent(agent || null);
      setValue('agent_id', parseInt(agentId));
    } else {
      setSelectedAgent(null);
      setValue('agent_id', undefined);
    }
  };

  return (
    <div className="space-y-6">
      <h3 className="text-xl font-semibold text-gray-900 mb-6">Preferensi Pekerjaan</h3>
      
      {/* Preferred Positions */}
      <div>
        <div className="flex items-center justify-between mb-4">
          <h4 className="text-lg font-medium text-gray-900">Posisi yang Diinginkan</h4>
          <button
            type="button"
            onClick={() => appendPosition({ position: '' })}
            className="flex items-center px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <Plus className="h-4 w-4 mr-1" />
            Tambah Posisi
          </button>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {positionFields.map((field, index) => (
            <div key={field.id} className="flex items-center space-x-2">
              <input
                {...register(`preferred_positions.${index}.position` as const)}
                className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Backend Developer, Frontend Developer, dll."
              />
              <button
                type="button"
                onClick={() => removePosition(index)}
                className="text-red-600 hover:text-red-800"
              >
                <X className="h-4 w-4" />
              </button>
            </div>
          ))}
        </div>
        
        {positionFields.length === 0 && (
          <div className="text-center py-6 text-gray-500">
            Belum ada posisi yang diinginkan. Klik "Tambah Posisi" untuk mulai menambahkan.
          </div>
        )}
      </div>

      {/* Preferred Locations */}
      <div>
        <div className="flex items-center justify-between mb-4">
          <h4 className="text-lg font-medium text-gray-900">Lokasi Kerja yang Diinginkan</h4>
          <button
            type="button"
            onClick={() => appendLocation({ location: '' })}
            className="flex items-center px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <Plus className="h-4 w-4 mr-1" />
            Tambah Lokasi
          </button>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {locationFields.map((field, index) => (
            <div key={field.id} className="flex items-center space-x-2">
              <input
                {...register(`preferred_locations.${index}.location` as const)}
                className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Jakarta, Bandung, dll."
              />
              <button
                type="button"
                onClick={() => removeLocation(index)}
                className="text-red-600 hover:text-red-800"
              >
                <X className="h-4 w-4" />
              </button>
            </div>
          ))}
        </div>
        
        {locationFields.length === 0 && (
          <div className="text-center py-6 text-gray-500">
            Belum ada lokasi yang diinginkan. Klik "Tambah Lokasi" untuk mulai menambahkan.
          </div>
        )}
      </div>

      {/* Salary Expectations */}
      <div>
        <h4 className="text-lg font-medium text-gray-900 mb-4">Ekspektasi Gaji</h4>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Gaji Minimum (Rp)
            </label>
            <input
              {...register('expected_salary_min')}
              type="number"
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="7000000"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Gaji Maksimum (Rp)
            </label>
            <input
              {...register('expected_salary_max')}
              type="number"
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="10000000"
            />
          </div>
        </div>
      </div>

      {/* Agent Selection */}
      <div>
        <h4 className="text-lg font-medium text-gray-900 mb-4">Informasi Rujukan</h4>
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Agent Perujuk
            </label>
            {loadingAgents ? (
              <div className="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                <span className="text-gray-500">Memuat data agent...</span>
              </div>
            ) : (
              <select
                {...register('agent_id')}
                onChange={(e) => handleAgentChange(e.target.value)}
                defaultValue={selectedAgentId || ''}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Pilih Agent (Opsional)</option>
                {agents.map((agent) => (
                  <option key={agent.id} value={agent.id}>
                    {agent.user.full_name} - {agent.agent_code} ({agent.referral_code})
                  </option>
                ))}
              </select>
            )}
            <p className="mt-1 text-xs text-gray-500">
              Pilih agent jika Anda dirujuk oleh seseorang
            </p>
          </div>

          {/* Selected Agent Info */}
          {selectedAgent && (
            <div className="p-4 bg-blue-50 border border-blue-200 rounded-lg">
              <div className="flex items-start space-x-3">
                <div className="p-2 bg-blue-500 rounded-full">
                  <UserCheck className="h-4 w-4 text-white" />
                </div>
                <div className="flex-1">
                  <h5 className="font-medium text-blue-900">Agent Terpilih</h5>
                  <p className="text-sm text-blue-700">
                    <strong>{selectedAgent.user.full_name}</strong>
                  </p>
                  <div className="mt-2 grid grid-cols-2 gap-2 text-xs text-blue-600">
                    <div>
                      <span className="font-medium">Kode Agent:</span> {selectedAgent.agent_code}
                    </div>
                    <div>
                      <span className="font-medium">Kode Rujukan:</span> {selectedAgent.referral_code}
                    </div>
                    <div>
                      <span className="font-medium">Level:</span> {selectedAgent.level.toUpperCase()}
                    </div>
                    <div>
                      <span className="font-medium">Total Rujukan:</span> {selectedAgent.total_referrals}
                    </div>
                  </div>
                  <p className="mt-2 text-xs text-blue-600">
                    <span className="font-medium">Kontak:</span> {selectedAgent.user.phone}
                  </p>
                </div>
              </div>
            </div>
          )}

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Catatan
            </label>
            <textarea
              {...register('notes')}
              rows={3}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Catatan tambahan tentang preferensi kerja atau informasi lain yang ingin disampaikan..."
            />
          </div>
        </div>
      </div>
    </div>
  );
}
