import { defineStore } from 'pinia';
import { Trip, TripPoint } from '@/api';

interface MapState {
  /** Currently selected trip */
  trip: Trip | null;
  /** Currently selected point of the trip */
  point: TripPoint | null;
  /** Variable for handling map clicks for clearing selections */
  clicks: number;
}

export const useMapStore = defineStore('map', {
  state: (): MapState => {
    return {
      trip: null,
      point: null,
      clicks: 0,
    }
  },
  actions: {
   //
  },
});
