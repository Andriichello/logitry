import { defineStore } from 'pinia';
import { Point, Route, Trip, TripPoint } from '@/api';

interface MapState {
  /** Currently selected route */
  route: Route | null;
  /** Currently selected point of the route */
  point: Point | null;
  /** Variable for handling map clicks for clearing selections */
  clicks: number;
}

export const useMapStore = defineStore('map', {
  state: (): MapState => {
    return {
      route: null,
      point: null,
      clicks: 0,
    }
  },
  actions: {
   //
  },
});
