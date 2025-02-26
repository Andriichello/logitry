import { defineStore } from 'pinia';
import { Point, Route, Trip, TripPoint } from '@/api';

interface MapState {
  /** Currently selected route */
  route: Route | null;
  /** Currently selected trip */
  trip: Trip | null;
  /** Currently selected point of the route */
  point: Point | null;
  /** Variable for handling map clicks for clearing selections */
  clicks: number;
  /** Determines if route points list is collapsed or not */
  arePointsHidden: boolean;
}

export const useMapStore = defineStore('map', {
  state: (): MapState => {
    return {
      route: null,
      trip: null,
      point: null,
      clicks: 0,
      arePointsHidden: false,
    }
  },
  actions: {
   //
  },
});
