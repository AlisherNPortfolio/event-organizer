export function useParticipants() {
    const getParticipants = async (eventId, token = null) => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        };

        // Agar auth bo'lsa token qo'shamiz
        if (token) {
            headers['Authorization'] = 'Bearer ' + token;
        }

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        const response = await fetch(`/api/v1/events/${eventId}/participants`, {
            method: 'GET',
            headers: headers
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    const toggleAttendanceModal = async (eventId, token) => {
        // const modal = document.getElementById('attendanceModal');
        // modal.classList.remove('hidden');

        try {
            const data = await getParticipants(eventId, token);

            if (data.participants && data.participants.length > 0) {
                let participants = data.participants || [];
                return {
                    success: true,
                    type: 'data',
                    data: participants
                };

            } else {
                // document.getElementById('attendance-list').innerHTML = `
                //     <div class="text-center py-8 text-gray-500">Hali hech kim qatnashmagan</div>
                // `;
                return {
                    success: false,
                    type: 'no-data',
                    message: 'Hali hech kim qatnashmagan'
                };
            }
        } catch (error) {
            console.log(error)
            return {
                success: false,
                type: 'error',
                message: 'Tarmoq xatoligi yuz berdi'
            }
            // document.getElementById('attendance-list').innerHTML = `
            //     <div class="text-center py-4 text-red-600">
            //         <p>Tarmoq xatoligi yuz berdi</p>
            //     </div>
            // `;
        }
    }

    const renderParticipantsList = (participants) => {
        const container = document.getElementById('participants-list');

        if (participants.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <p>Hozircha qatnashchilar yo'q</p>
                </div>
            `;
            return;
        }

        const participantsHtml = participants.map(participant => `
            <div class="flex items-center justify-between p-3 border-b border-gray-200 last:border-b-0">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        ${participant.avatar_url ?
                            `<img class="h-10 w-10 rounded-full object-cover" src="${participant.avatar_url}" alt="${participant.name}">` :
                            `<div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">${participant.name.charAt(0).toUpperCase()}</span>
                            </div>`
                        }
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">${participant.name}</p>
                        <p class="text-xs text-gray-500">Reyting: ${participant.rating} ball</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    ${participant.attendance_status ?
                        `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                            participant.attendance_status === 'attended' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        }">
                            ${participant.attendance_status === 'attended' ? 'Qatnashgan' : 'Qatnashmagan'}
                        </span>` :
                        `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                            Belgilanmagan
                        </span>`
                    }
                </div>
            </div>
        `).join('');

        container.innerHTML = participantsHtml;
    }

    return {
        getParticipants,
        toggleAttendanceModal,
        renderParticipantsList
    }
}
